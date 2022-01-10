<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Page\Ajax;

use PeServer\Core\Mvc\LogicCallMode;
use PeServer\App\Models\ResponseJson;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\Domains\Page\PageLogicBase;
use PeServer\App\Models\Domains\UserLevel;
use PeServer\App\Models\SessionManager;
use PeServer\Core\ArrayUtility;
use PeServer\Core\Mime;
use PeServer\Core\Mvc\Markdown;
use PeServer\Core\TypeConverter;

class AjaxMarkdownLogic extends PageLogicBase
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
		$account = SessionManager::getAccount();

		$json = $this->getRequestJson();

		$isSafeMode = TypeConverter::parseBoolean(ArrayUtility::getOr($json, 'safe_mode', true));
		$source = ArrayUtility::getOr($json, 'source', '');
		if ($account['level'] !== UserLevel::ADMINISTRATOR) {
			$isSafeMode = true;
		}

		$markdown = new Markdown();
		$markdown->setSafeMode($isSafeMode);
		$result = $markdown->build($source);

		$this->setResponseJson(ResponseJson::success(['markdown' => $result]));
	}
}
