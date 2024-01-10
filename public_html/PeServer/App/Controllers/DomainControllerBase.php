<?php

declare(strict_types=1);

namespace PeServer\App\Controllers;

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AppTemplateOptions;
use PeServer\App\Models\AppUrl;
use PeServer\App\Models\AppViewActionResult;
use PeServer\Core\DI\Inject;
use PeServer\Core\Mvc\ControllerArgument;
use PeServer\Core\Mvc\ControllerBase;
use PeServer\Core\Mvc\Result\RedirectActionResult;
use PeServer\Core\Mvc\Result\ViewActionResult;
use PeServer\Core\Mvc\Template\ITemplateFactory;
use PeServer\Core\Mvc\Template\TemplateParameter;
use PeServer\Core\Web\IUrlHelper;
use PeServer\Core\Web\UrlPath;
use PeServer\Core\Web\UrlQuery;
use PeServer\Core\Web\WebSecurity;

/**
 * アプリケーションコントローラ基底処理。
 */
abstract class DomainControllerBase extends ControllerBase
{
	#region variable

	#[Inject] //@phpstan-ignore-next-line [INJECT]
	private AppUrl $appUrl;

	#endregion

	protected function __construct(ControllerArgument $argument)
	{
		parent::__construct($argument);
	}

	#region ControllerBase

	protected function createViewActionResult(
		string $templateBaseName,
		string $actionName,
		TemplateParameter $templateParameter,
		array $headers,
		ITemplateFactory $templateFactory,
		IUrlHelper $urlHelper,
		WebSecurity $webSecurity
	): ViewActionResult {
		return new AppViewActionResult($templateBaseName, $actionName, $templateParameter, $headers, $templateFactory, $urlHelper, $webSecurity);
	}

	protected function redirectPath(UrlPath|string $path, ?UrlQuery $query = null): RedirectActionResult
	{
		//NOTE: リダイレクトURLは設定から取得するためオーバーライドしている

		if (is_string($path)) {
			$path = new UrlPath($path);
		}

		$url = $this->appUrl->addPublicUrl($path, $query);

		return parent::redirectUrl($url);
	}

	#endregion
}
