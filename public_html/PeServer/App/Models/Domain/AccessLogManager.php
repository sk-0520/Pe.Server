<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain;

use DateTimeImmutable;
use PeServer\App\Models\AppConfiguration;
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

class AccessLogManager
{
	#region variable

	private ILogger $logger;

	#endregion

	public function __construct(
		private IDatabaseConnection $databaseConnection,
		private AppConfiguration $appConfig,
		private SpecialStore $specialStore,
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

		$logParams = Logging::getLogParameters(new DateTimeImmutable(), $this->specialStore);
		$logParams["RUNNING_TIME"] = microtime(true) - $this->specialStore->getServer('REQUEST_TIME_FLOAT', 0.0);

		$data = $jsonSerializer->save($logParams);
		$log = new Binary($data . PHP_EOL);
		//$this->logger->trace("{0}", $data);
		File::appendContent($filePath, $log);
	}

	public function vacuum(): void
	{
		$files = Directory::find($this->appConfig->setting->accessLog->directory, "*.log");
		$contents = [];
		foreach ($files as $file) {
			$contents[] = File::readContent($file);
			//File::removeFile($file);
		}
	}

	#endregion
}
