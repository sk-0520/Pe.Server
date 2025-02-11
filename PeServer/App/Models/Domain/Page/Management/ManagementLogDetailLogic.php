<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Management;

use ZipArchive;
use PeServer\Core\Mime;
use PeServer\Core\Binary;
use PeServer\Core\Archiver;
use PeServer\Core\IO\File;
use PeServer\Core\IO\Path;
use PeServer\Core\Text;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AppTemporary;
use PeServer\Core\Throws\FileNotFoundException;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\App\Models\SessionKey;
use PeServer\Core\ArchiveEntry;
use PeServer\Core\Mvc\Content\FileCleanupStream;
use PeServer\Core\Throws\InvalidOperationException;

ini_set('memory_limit', '-1');

class ManagementLogDetailLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter, private AppConfiguration $config, private AppTemporary $appTemporary)
	{
		parent::__construct($parameter);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NOP
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$logging = $this->config->setting->logging;
		/** @var string */
		$dirPath = $logging->loggers['file']->configuration['directory'];

		$fileName = Text::trim($this->getRequest('log_name'), '/\\.');
		$filePath = Path::combine($dirPath, $fileName);
		if (!File::exists($filePath)) {
			throw new FileNotFoundException();
		}

		$binary = File::readContent($filePath);

		$archiveSize = $logging->archiveSize;
		$fileSize = File::getFileSize($filePath);

		if ($archiveSize <= $fileSize || $callMode === LogicCallMode::Submit) {
			$this->result['download'] = true;

			$userInfo = $this->getAuditUserInfo();
			if ($userInfo === null) {
				throw new InvalidOperationException();
			}
			$userId = $userInfo->getUserId();

			$workDirPath = $this->appTemporary->getLogDownloadDirectory($userId);
			$zipFileName = Path::getFileNameWithoutExtension($fileName);
			$zipPath = Path::combine($workDirPath, "{$zipFileName}.zip");

			Archiver::compressZip(
				$zipPath,
				[
					// @phpstan-ignore argument.type, argument.type
					new ArchiveEntry($filePath, $fileName)
				]
			);

			$stream = FileCleanupStream::read($zipPath);
			$this->setDownloadContent(Mime::ZIP, "{$zipFileName}.zip", $stream);
		} else {
			$this->setValue('log_name', $fileName);
			$this->setValue('log_file', $filePath);
			$this->setValue('log_value', $binary->raw);
		}
	}
}
