<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Setting;

use PeServer\Core\IOUtility;
use PeServer\Core\PathUtility;
use PeServer\Core\SizeConverter;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\Domain\Page\PageLogicBase;


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
		/** @var array<string,mixed> */
		$logging = AppConfiguration::$config['logging'];
		/** @var string @-phpstan-ignore-next-line */
		$dirPath = $logging['file']['directory'];
		/** @var string @-phpstan-ignore-next-line */
		$targetExt = PathUtility::getFileExtension($logging['file']['name']);
		$files = IOUtility::getFiles($dirPath, false);

		$logFiles = array_filter($files, function ($i) use ($targetExt) {
			return PathUtility::getFileExtension($i) === $targetExt;
		});
		natsort($logFiles);
		$logFiles = array_map(function ($i) {
			$sizeConverter = new SizeConverter();
			$size = IOUtility::getFileSize($i);
			return [
				'directory' => PathUtility::getDirectoryPath($i),
				'name' => PathUtility::getFileName($i),
				'size' => $size,
				'human_size' => $sizeConverter->convertHumanReadableByte($size, '{f_size} {unit}'),
			];
		}, $logFiles);

		$this->setValue('directory', $dirPath);
		$this->setValue('log_files', $logFiles);
	}
}
