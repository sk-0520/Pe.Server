<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Setting;

use PeServer\App\Models\AppConfiguration;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\FileUtility;
use PeServer\Core\SizeConverter;

class SettingLogListLogic extends PageLogicBase
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
		$logging = AppConfiguration::$config['logging'];
		$dirPath = (string)$logging['file']['directory'];
		$targetExt = FileUtility::getFileExtension($logging['file']['name']);
		$files = FileUtility::getFiles($dirPath, false);

		$logFiles = array_filter($files, function ($i) use ($targetExt) {
			return FileUtility::getFileExtension($i) === $targetExt;
		});
		natsort($logFiles);
		$logFiles = array_map(function ($i) {
			$sizeConverter = new SizeConverter();
			$size = FileUtility::getFileSize($i);
			return [
				'directory' => FileUtility::getDirectoryPath($i),
				'name' => FileUtility::getFileName($i),
				'size' => $size,
				'human_size' => $sizeConverter->convertHumanReadableByte($size, '{f_size} {term}'),
			];
		}, $logFiles);

		$this->setValue('directory', $dirPath);
		$this->setValue('log_files', $logFiles);
	}
}
