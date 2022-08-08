<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Home;

use PeServer\Core\IOUtility;
use PeServer\Core\PathUtility;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Environment;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mime;
use PeServer\Core\StringUtility;
use PeServer\Core\Throws\HttpStatusException;

class HomeWildcardLogic extends PageLogicBase
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
		$requestPath = $this->getRequest('path');

		$favicon = 'favicon.ico';
		if (StringUtility::startsWith($requestPath, $favicon, true)) {
			$path = PathUtility::combine(AppConfiguration::$rootDirectoryPath, 'assets', $favicon);
			$this->setContent(Mime::ICON, IOUtility::readContent($path));
			return;
		}

		throw new HttpStatusException(HttpStatus::notFound(), $requestPath);
	}
}
