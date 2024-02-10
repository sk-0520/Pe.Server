<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\ManagementControl;

use PeServer\App\Models\Domain\AppArchiver;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Collection\Arr;
use PeServer\Core\Collection\OrderBy;
use PeServer\Core\IO\File;
use PeServer\Core\IO\Path;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\SizeConverter;

class ManagementControlBackupListLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter, private AppArchiver $appArchiver)
	{
		parent::__construct($parameter);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NOP
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$files = Arr::sortByValue($this->appArchiver->getFiles(), OrderBy::Descending);
		$sizeConverter = new SizeConverter();
		/** @var array<array{name:string,size:int}> */
		$items = [];
		foreach ($files as $file) {
			$fileSize = File::getFileSize($file);
			$item = [
				'name' => Path::getFileName($file),
				'size' => $fileSize,
				'human_size' => $sizeConverter->convertHumanReadableByte($fileSize, '{f_size} {unit}'),
			];
			$items[] = $item;
		}

		$this->setValue('items', $items);
		$this->setValue('directory', $this->appArchiver->getDirectory());
	}
}
