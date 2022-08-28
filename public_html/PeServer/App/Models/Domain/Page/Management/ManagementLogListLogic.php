<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Management;

use PeServer\Core\IO\Directory;
use PeServer\Core\IO\File;
use PeServer\Core\IO\Path;
use PeServer\Core\SizeConverter;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\Domain\Page\PageLogicBase;


class ManagementLogListLogic extends PageLogicBase
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
		$logging = $this->config->setting->logging;
		/** @var string @-phpstan-ignore-next-line */
		$dirPath = $logging->loggers['file']->configuration['directory'];
		/** @var string @-phpstan-ignore-next-line */
		$targetExt = Path::getFileExtension($logging->loggers['file']->configuration['name']);
		$files = Directory::getFiles($dirPath, false);

		$logFiles = array_filter($files, function ($i) use ($targetExt) {
			return Path::getFileExtension($i) === $targetExt;
		});
		natsort($logFiles);
		$logFiles = array_map(function ($i) {
			$sizeConverter = new SizeConverter();
			$size = File::getFileSize($i);
			return [
				'directory' => Path::getDirectoryPath($i),
				'name' => Path::getFileName($i),
				'size' => $size,
				'human_size' => $sizeConverter->convertHumanReadableByte($size, '{f_size} {unit}'),
			];
		}, $logFiles);

		$this->setValue('directory', $dirPath);
		$this->setValue('log_files', $logFiles);
	}
}
