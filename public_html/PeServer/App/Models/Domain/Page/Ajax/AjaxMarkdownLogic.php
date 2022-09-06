<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Ajax;

use PeServer\Core\Collections\Arr;
use PeServer\Core\Text;
use PeServer\Core\Mvc\Markdown;
use PeServer\Core\TypeUtility;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\App\Models\ResponseJson;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\SessionKey;
use PeServer\App\Models\Domain\UserLevel;
use PeServer\App\Models\Domain\Page\PageLogicBase;

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
		$account = $this->requireSession(SessionKey::ACCOUNT);

		$json = $this->getRequestJson();

		$isSafeMode = TypeUtility::parseBoolean(Arr::getOr($json, 'safe_mode', true));
		/** @var string */
		$source = Arr::getOr($json, 'source', Text::EMPTY);
		if ($account->level !== UserLevel::ADMINISTRATOR) {
			$isSafeMode = true;
		}

		$markdown = new Markdown();
		$markdown->setSafeMode($isSafeMode);
		$result = $markdown->build($source);

		$this->setResponseJson(ResponseJson::success(['markdown' => $result]));
	}
}
