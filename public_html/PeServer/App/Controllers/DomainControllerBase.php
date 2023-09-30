<?php

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

	/**
	 * Undocumented function
	 *
	 * @param string $templateBaseName
	 * @param string $actionName
	 * @param TemplateParameter $templateParameter
	 * @param array $headers
	 * @phpstan-param array<non-empty-string,string[]> $headers
	 * @param ITemplateFactory $templateFactory
	 * @param IUrlHelper $urlHelper
	 */
	protected function createViewActionResult(
		string $templateBaseName,
		string $actionName,
		TemplateParameter $templateParameter,
		array $headers,
		ITemplateFactory $templateFactory,
		IUrlHelper $urlHelper
	): ViewActionResult {
		return new AppViewActionResult($templateBaseName, $actionName, $templateParameter, $headers, $templateFactory, $urlHelper);
	}

	// protected function redirectPath(UrlPath|string $path, ?UrlQuery $query = null): RedirectActionResult
	// {
	// 	return parent::redirectPath($path, $query);
	// }

	#endregion
}
