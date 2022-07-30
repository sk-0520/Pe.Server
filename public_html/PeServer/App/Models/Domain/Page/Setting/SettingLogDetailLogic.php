<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Setting;

use PeServer\Core\Mime;
use PeServer\Core\Binary;
use PeServer\Core\Archiver;
use PeServer\Core\IOUtility;
use PeServer\Core\PathUtility;
use PeServer\Core\StringUtility;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\AppConfiguration;
use PeServer\Core\Throws\FileNotFoundException;
use PeServer\App\Models\Domain\Page\PageLogicBase;

class SettingLogDetailLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NONE
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		/** @var array<string,mixed> */
		$logging = AppConfiguration::$config['logging'];
		/** @var string @-phpstan-ignore-next-line */
		$dirPath = (string)$logging['file']['directory'];

		$fileName = StringUtility::trim($this->getRequest('log_name'), '/\\.');
		$filePath = PathUtility::joinPath($dirPath, $fileName);
		if (!IOUtility::existsFile($filePath)) {
			throw new FileNotFoundException();
		}

		$binary = IOUtility::readContent($filePath);

		/** @var int @-phpstan-ignore-next-line */
		$archiveSize = AppConfiguration::$config['logging']['archive_size'];
		$fileSize = IOUtility::getFileSize($filePath);

		if ($archiveSize <= $fileSize) {
			$this->result['download'] = true;

			$compressed = Archiver::compressGzip($binary, 9);

			$this->setDownloadContent(Mime::GZ, $fileName . '.gz', $compressed);
		} else {
			$this->setValue('log_name', $fileName);
			$this->setValue('log_file', $filePath);
			$this->setValue('log_value', $binary->getRaw());
		}
	}
}
