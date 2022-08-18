<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Setting;

use PeServer\Core\Mime;
use PeServer\Core\Binary;
use PeServer\Core\Archiver;
use PeServer\Core\IO\File;
use PeServer\Core\IO\Path;
use PeServer\Core\Text;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\AppConfiguration;
use PeServer\Core\Throws\FileNotFoundException;
use PeServer\App\Models\Domain\Page\PageLogicBase;

ini_set('memory_limit', '-1');

class SettingLogDetailLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter, private AppConfiguration $config)
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
		$logging = $this->config->setting['logging'];
		/** @var string @-phpstan-ignore-next-line */
		$dirPath = $logging['file']['configuration']['logger']['directory'];

		$fileName = Text::trim($this->getRequest('log_name'), '/\\.');
		$filePath = Path::combine($dirPath, $fileName);
		if (!File::exists($filePath)) {
			throw new FileNotFoundException();
		}

		$binary = File::readContent($filePath);

		/** @var int @-phpstan-ignore-next-line */
		$archiveSize = $this->config->setting['logging']['archive_size'];
		$fileSize = File::getFileSize($filePath);

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
