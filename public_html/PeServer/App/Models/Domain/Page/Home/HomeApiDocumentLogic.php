<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Home;

use PeServer\Core\IO\File;
use PeServer\Core\IO\Path;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\Domain\Page\PageLogicBase;


class HomeApiDocumentLogic extends PageLogicBase
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
		$apiDocumentPath = Path::combine(AppConfiguration::$settingDirectoryPath, 'api_document.md');
		$apiDocument = File::readContent($apiDocumentPath);
		$this->setValue('api_document', $apiDocument->getRaw());
	}
}
