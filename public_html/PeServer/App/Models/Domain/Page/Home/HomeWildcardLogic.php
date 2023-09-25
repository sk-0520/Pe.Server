<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Home;

use PeServer\Core\IO\IOUtility;
use PeServer\Core\IO\Path;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Environment;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\IO\File;
use PeServer\Core\Mime;
use PeServer\Core\Text;
use PeServer\Core\Throws\HttpStatusException;

class HomeWildcardLogic extends PageLogicBase
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
		$unsafeRequestPath = $this->getRequest('path');

		$requestFileName = Path::getFileName($unsafeRequestPath);
		$assetsDirPath = Path::combine($this->config->rootDirectoryPath, 'assets');
		$targetPath = Path::combine($assetsDirPath, $requestFileName);

		if (Text::startsWith($targetPath, $assetsDirPath, true)) {
			if (File::exists($targetPath)) {
				$this->setFileContent(null, $targetPath);
				return;
			}
		}


		throw new HttpStatusException(HttpStatus::NotFound, $unsafeRequestPath);
	}
}
