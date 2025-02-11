<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Account;

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\Configuration\AppSetting;
use PeServer\App\Models\Dao\Entities\UserAuditLogsEntityDao;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Archiver;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\File;
use PeServer\Core\IO\Path;
use PeServer\Core\IO\Stream;
use PeServer\Core\Mime;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Mvc\Pagination;
use PeServer\Core\Serialization\JsonSerializer;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\TypeUtility;
use PeServer\Core\Utc;
use ZipArchive;

class AccountUserAuditLogDownloadLogic extends PageLogicBase
{
	#region define

	public const RAW_LOG_SIZE = 2 * 1024 * 1024;

	#endregion

	public function __construct(LogicParameter $parameter, private AppConfiguration $appConfig)
	{
		parent::__construct($parameter);
	}

	#region PageLogicBase

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NOP
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$userInfo = $this->getAuditUserInfo();
		if ($userInfo === null) {
			throw new InvalidOperationException();
		}
		$userId = $userInfo->getUserId();

		// $pageNumber = Pagination::FIRST_PAGE_NUMBER;
		// if ($callMode === LogicCallMode::Submit) {
		// 	$requestPageNumber = $this->getRequest('page_number');
		// 	if (TypeUtility::tryParseInteger($requestPageNumber, $temp)) {
		// 		$pageNumber = $temp;
		// 	}
		// }

		$database = $this->openDatabase();
		$userAuditLogsEntityDao = new UserAuditLogsEntityDao($database);

		$result = $userAuditLogsEntityDao->selectAuditLogsFromUserId($userId);
		$jsonSerializer = new JsonSerializer();
		$items = $jsonSerializer->save($result->rows);

		if ($items->count() < self::RAW_LOG_SIZE) {
			$this->setDownloadContent(Mime::JSON, "audit-log.json", $items);
		} else {
			$dirPath = Path::combine($this->appConfig->setting->cache->temporary, "audit", $userId);
			Directory::createDirectory($dirPath);
			$this->logger->info("audit temp dir: {0}", $dirPath);
			$baseFileName = $userId . "_" .  $this->beginTimestamp->format('Y-m-d\_His');
			$auditFilePath = Path::combine($dirPath, "{$baseFileName}.log");
			$zipFilePath = Path::combine($dirPath, "{$baseFileName}.zip");
			File::writeContent($auditFilePath, $items);
			$zipArchive = new ZipArchive();
			$zipArchive->open($zipFilePath, ZipArchive::CREATE | ZipArchive::EXCL);
			$zipArchive->addFile($auditFilePath, "{$baseFileName}.log");
			$zipArchive->close();
			File::removeFile($auditFilePath);
			$data = Stream::open($zipFilePath, Stream::MODE_READ);
			$this->setDownloadContent(Mime::ZIP, "audit-log.zip", $data);
		}
	}

	#endregion
}
