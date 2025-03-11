<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Management;

use Throwable;
use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AppDatabaseConnection;
use PeServer\App\Models\AppTemporary;
use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Archive\ArchiveEntry;
use PeServer\Core\Archive\Archiver;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Database\DatabaseTableResult;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\DI\IDiContainer;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\File;
use PeServer\Core\IO\Path;
use PeServer\Core\IO\Stream;
use PeServer\Core\Mime;
use PeServer\Core\Mvc\Content\FileCleanupStream;
use PeServer\Core\Mvc\DownloadFileContent;
use PeServer\Core\Mvc\Logic\LogicCallMode;
use PeServer\Core\Mvc\Logic\LogicParameter;
use PeServer\Core\Mvc\Validator;
use PeServer\Core\Regex;
use PeServer\Core\Text;
use PeServer\Core\Throws\HttpStatusException;
use PeServer\Core\Throws\InvalidOperationException;
use ZipArchive;

class ManagementDatabaseDownloadLogic extends ManagementDatabaseBase
{
	public function __construct(LogicParameter $parameter, AppConfiguration $appConfig, private AppTemporary $appTemporary)
	{
		parent::__construct($parameter, $appConfig);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NOP
	}


	protected function executeImpl(LogicCallMode $callMode): void
	{
		if ($callMode !== LogicCallMode::Submit) {
			throw new HttpStatusException(HttpStatus::InternalServerError);
		}

		$userInfo = $this->getAuditUserInfo();
		if ($userInfo === null) {
			throw new InvalidOperationException();
		}
		$userId = $userInfo->getUserId();

		$workDirPath = $this->appTemporary->getDatabaseDownloadDirectory($userId);
		$this->logger->info("database temp dir: {0}", $workDirPath);
		$zipFilePath = Path::combine($workDirPath, $this->appTemporary->createFileName($this->beginTimestamp, "zip"));

		$target = AppDatabaseConnection::getSqliteFilePath($this->appConfig->setting->persistence->default->connection);
		$name = Path::getFileName($target);

		Archiver::compressZip(
			$zipFilePath,
			[
				// @phpstan-ignore argument.type, argument.type
				new ArchiveEntry($target, $name)
			]
		);

		$this->writeAuditLogCurrentUser(AuditLog::ADMINISTRATOR_DOWNLOAD_DATABASE, [
			"path" => $target,
			"size" => File::getFileSize($target),
		]);

		$stream = FileCleanupStream::read($zipFilePath);
		$this->setDownloadContent(Mime::ZIP, "database.zip", $stream);
	}
}
