<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\ManagementControl;

use PeServer\App\Models\Domain\AppArchiver;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Collections\OrderBy;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\IO\File;
use PeServer\Core\IO\Path;
use PeServer\Core\Mime;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\SizeConverter;
use PeServer\Core\Throws\HttpStatusException;

class ManagementControlBackupDownloadLogic extends PageLogicBase
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
		$unsafeFileName = $this->getRequest('file_name');
		$safeFileName = Path::getFileName($unsafeFileName);
		if ($safeFileName !== $unsafeFileName) {
			$this->logger->warn('これはもう攻撃: {0}', $unsafeFileName);
			throw new HttpStatusException(HttpStatus::NotFound);
		}

		$filePath = Path::combine($this->appArchiver->getDirectory(), $safeFileName);
		if (!File::exists($filePath)) {
			throw new HttpStatusException(HttpStatus::NotFound);
		}

		$this->setDownloadContent(Mime::ZIP, $safeFileName, File::readContent($filePath));
	}
}
