<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain;

use DateTimeImmutable;
use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\Dao\Entities\AccessLogsEntityDao;
use PeServer\App\Models\Data\Dto\AccessLogDto;
use PeServer\Core\Binary;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Database\IDatabaseConnection;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\File;
use PeServer\Core\IO\Path;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Log\Logging;
use PeServer\Core\Serialization\JsonSerializer;
use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Text;

class AccessLogManager
{
	#region variable

	private ILogger $logger;

	#endregion

	public function __construct(
		private IDatabaseConnection $databaseConnection,
		private AppConfiguration $appConfig,
		private SpecialStore $specialStore,
		private Logging $logging,
		ILoggerFactory $loggerFactory
	) {
		$this->logger = $loggerFactory->createLogger($this);
	}

	#region function

	public function put(): void
	{
		$jsonSerializer = new JsonSerializer(JsonSerializer::SAVE_NONE, JsonSerializer::LOAD_NONE);

		$filePath = Path::combine($this->appConfig->setting->accessLog->directory, date('Ymd') . '.log');
		Directory::createParentDirectoryIfNotExists($filePath);

		$logParams = $this->logging->getLogParameters(new DateTimeImmutable(), $this->specialStore);
		$logParams["RUNNING_TIME"] = \microtime(true) - $this->specialStore->getServer('REQUEST_TIME_FLOAT', 0.0);

		$data = $jsonSerializer->save($logParams);
		$log = new Binary($data . PHP_EOL);
		//$this->logger->trace("{0}", $data);
		File::appendContent($filePath, $log);
	}

	public function vacuum(): void
	{
		$jsonSerializer = new JsonSerializer(JsonSerializer::SAVE_NONE, JsonSerializer::LOAD_NONE);

		$files = Directory::find($this->appConfig->setting->accessLog->directory, "*.log");
		/** @var Binary[] */
		$contents = [];
		foreach ($files as $file) {
			$contents[] = File::readContent($file);
			$this->logger->info('アクセスログ削除: {0}', $file);
			File::removeFile($file);
		}

		/** @var AccessLogDto[] */
		$accessLogs = [];
		foreach ($contents as $content) {
			$lines = Text::splitLines($content->raw);
			foreach ($lines as $line) {
				if (Text::isNullOrWhiteSpace($line)) {
					continue;
				}

				$raw = $jsonSerializer->load(new Binary($line));
				assert(is_array($raw));

				$request = $raw['REQUEST'];
				$pqf = Text::split($request, '?', 2);
				$p = $pqf[0];
				$q = $f = '';
				if (count($pqf) === 2) {
					$qf = Text::split($pqf[1], '#', 2);
					$q = $qf[0];
					if (count($qf) === 2) {
						$f = $qf[1];
					}
				}

				$dto = new AccessLogDto();
				$dto->timestamp = $raw['TIMESTAMP'];
				$dto->clientIp = $raw['CLIENT_IP'];
				$dto->clientHost = $raw['CLIENT_HOST'];
				$dto->requestId = $raw['REQUEST_ID'];
				$dto->session = $raw['SESSION'];
				$dto->ua = $raw['UA'];
				$dto->method = $raw['METHOD'];
				$dto->path = $p;
				$dto->query = $q;
				$dto->fragment = $f;
				$dto->referer = $raw['REFERER'];
				$dto->runningTime = $raw['RUNNING_TIME'];

				$accessLogs[] = $dto;
			}
		}

		if (count($accessLogs)) {
			$this->logger->info('アクセスログ件数: {0}', count($accessLogs));
			$database = $this->databaseConnection->open();
			$database->transaction(function ($context) use ($accessLogs) {
				$dao = new AccessLogsEntityDao($context);
				foreach ($accessLogs as $accessLog) {
					$dao->insertAccessLog($accessLog);
				}
				return true;
			});
		} else {
			$this->logger->info('アクセスログなし');
		}
	}

	#endregion
}
