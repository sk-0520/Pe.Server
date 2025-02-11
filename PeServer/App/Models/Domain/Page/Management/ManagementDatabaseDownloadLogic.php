<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Management;

use Throwable;
use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AppDatabaseConnection;
use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Archiver;
use PeServer\Core\Collection\Arr;
use PeServer\Core\Database\DatabaseTableResult;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\File;
use PeServer\Core\IO\Path;
use PeServer\Core\IO\Stream;
use PeServer\Core\Mime;
use PeServer\Core\Mvc\Content\FileCleanupStream;
use PeServer\Core\Mvc\DownloadFileContent;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Mvc\Validator;
use PeServer\Core\Regex;
use PeServer\Core\Text;
use PeServer\Core\Throws\HttpStatusException;
use PeServer\Core\Throws\InvalidOperationException;
use ZipArchive;

class ManagementDatabaseDownloadLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter, private AppConfiguration $config)
	{
		parent::__construct($parameter);
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

		$workDirPath = Path::combine($this->config->setting->cache->temporary, "database", $userId);
		Directory::createDirectory($workDirPath);
		$zipFilePath = Path::combine($workDirPath, $this->beginTimestamp->format('Y-m-d\_His') . ".zip");
		$zipArchive = new ZipArchive();
		$zipArchive->open($zipFilePath, ZipArchive::CREATE | ZipArchive::EXCL);

		$target = AppDatabaseConnection::getSqliteFilePath($this->config->setting->persistence->default->connection);
		$name = Path::getFileName($target);
		$zipArchive->addFile($target, $name);
		$zipArchive->close();

		$this->writeAuditLogCurrentUser(AuditLog::ADMINISTRATOR_DOWNLOAD_DATABASE, [
			"path" => $target,
			"size" => File::getFileSize($target),
		]);

		$stream = FileCleanupStream::read($zipFilePath);
		$this->setDownloadContent(Mime::ZIP, "database.zip", $stream);
	}
}
