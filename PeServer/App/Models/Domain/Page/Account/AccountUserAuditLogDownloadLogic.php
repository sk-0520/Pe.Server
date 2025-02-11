<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Account;

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AppTemporary;
use PeServer\App\Models\Configuration\AppSetting;
use PeServer\App\Models\Dao\Entities\UserAuditLogsEntityDao;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\ArchiveEntry;
use PeServer\Core\Archiver;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\File;
use PeServer\Core\IO\Path;
use PeServer\Core\IO\Stream;
use PeServer\Core\Mime;
use PeServer\Core\Mvc\Content\FileCleanupStream;
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

	public const RAW_LOG_SIZE = 1;
	//public const RAW_LOG_SIZE = 2 * 1024 * 1024;

	#endregion

	public function __construct(LogicParameter $parameter, private AppTemporary $appTemporary)
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
			$workDirPath = $this->appTemporary->getDatabaseDownloadDirectory($userId);
			$this->logger->info("audit temp dir: {0}", $workDirPath);
			$auditFilePath = Path::combine($workDirPath, $this->appTemporary->createFileName($this->beginTimestamp, "log"));
			$zipFilePath = Path::combine($workDirPath, $this->appTemporary->createFileName($this->beginTimestamp, "zip"));
			File::writeContent($auditFilePath, $items);

			Archiver::compressZip(
				$zipFilePath,
				[
					// @phpstan-ignore argument.type
					new ArchiveEntry($auditFilePath, "audit-log.json")
				]
			);
			File::removeFile($auditFilePath);

			$data = FileCleanupStream::read($zipFilePath);
			$this->setDownloadContent(Mime::ZIP, "audit-log.zip", $data);
		}
	}

	#endregion
}
