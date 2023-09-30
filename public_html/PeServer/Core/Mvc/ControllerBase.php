<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Log\Logging;
use PeServer\Core\Mvc\ControllerArgument;
use PeServer\Core\Mvc\DataContent;
use PeServer\Core\Mvc\LogicBase;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Mvc\Result\DataActionResult;
use PeServer\Core\Mvc\Result\IActionResult;
use PeServer\Core\Mvc\Result\RedirectActionResult;
use PeServer\Core\Mvc\Result\ViewActionResult;
use PeServer\Core\Mvc\Template\ITemplateFactory;
use PeServer\Core\Mvc\Template\TemplateParameter;
use PeServer\Core\ReflectionUtility;
use PeServer\Core\Store\Stores;
use PeServer\Core\Text;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\Web\IUrlHelper;
use PeServer\Core\Web\Url;
use PeServer\Core\Web\UrlPath;
use PeServer\Core\Web\UrlQuery;
use PeServer\Core\Web\UrlUtility;

/**
 * コントローラ基底処理。
 */
abstract class ControllerBase
{
	#region variable

	/**
	 * ロガー。
	 * @readonly
	 */
	protected ILogger $logger;

	/** @readonly */
	protected ILoggerFactory $loggerFactory;

	/** @readonly */
	protected Stores $stores;

	/** @readonly */
	protected ILogicFactory $logicFactory;
	/** @readonly */
	protected ITemplateFactory $templateFactory;
	/** @readonly */
	protected IUrlHelper $urlHelper;

	/** コントローラ内で今輝いてるロジック。よくないんよなぁ。 */
	protected ?LogicBase $logic = null;

	#endregion

	/**
	 * 生成。
	 *
	 * @param ControllerArgument $argument コントローラ入力値(継承先でも必須となる)。
	 */
	protected function __construct(ControllerArgument $argument)
	{
		$this->stores = $argument->stores;
		$this->logicFactory = $argument->logicFactory;
		$this->templateFactory = $argument->templateFactory;
		$this->urlHelper = $argument->urlHelper;
		$this->logger = $argument->logger;
		$this->loggerFactory = $argument->loggerFactory;
	}

	#region function

	/**
	 * コントローラ完全名からコントローラベース名を取得する際にスキップする文言(文字列長が使用される)
	 *
	 * @return string
	 */
	abstract protected function getSkipBaseName(): string;

	// /**
	//  * ロジック用パラメータ生成処理。
	//  *
	//  * @param string $logicName ロジック名
	//  * @phpstan-param class-string<LogicBase> $logicName
	//  * @param HttpRequest $request リクエストデータ
	//  * @return LogicParameter
	//  */
	// protected function createParameter(string $logicName, HttpRequest $request): LogicParameter
	// {
	// 	return new LogicParameter(
	// 		$request,
	// 		$this->stores,
	// 		Logging::create($logicName)
	// 	);
	// }

	/**
	 * ロジック生成処理。
	 *
	 * @param string $logicClass ロジック完全名。
	 * @phpstan-param class-string<LogicBase> $logicClass
	 * @param array<int|string,mixed> $arguments
	 * @return LogicBase
	 */
	protected function createLogic(string $logicClass, array $arguments = []): LogicBase
	{
		if ($this->logic !== null) {
			throw new InvalidOperationException();
		}

		// $parameter = $this->createParameter($logicClass, $request);
		// /** @var LogicBase */
		// $logic = ReflectionUtility::create($logicClass, LogicBase::class, $parameter, ...$parameters);

		$logic = $this->logicFactory->createLogic($logicClass, $arguments/*, ...$parameters*/);

		$this->logic = $logic;
		return $logic;
	}

	/**
	 * ロジック側で生成された応答ヘッダを取得。
	 *
	 * @return array<string,string[]> 応答ヘッダ。ロジック未生成の場合は空の応答ヘッダを返す。
	 * @phpstan-return array<non-empty-string,string[]> 応答ヘッダ。ロジック未生成の場合は空の応答ヘッダを返す。
	 */
	private function getResponseHeaders(): array
	{
		/** @phpstan-var array<non-empty-string,string[]> */
		$headers = [];

		if ($this->logic !== null) {
			$headers = $this->logic->getResponseHeaders();
		}

		return $headers;
	}

	/**
	 * URLリダイレクト。
	 *
	 * @param Url $url
	 * @return RedirectActionResult
	 */
	protected function redirectUrl(Url $url): RedirectActionResult
	{
		return new RedirectActionResult($url, HttpStatus::Found);
	}

	/**
	 * ドメイン内でリダイレクト。
	 * 基本的にこれを使っておけばいいが、ドメイン周りはそれっぽく取得しているだけなので正確に対応するなら継承先でいい感じにすること。
	 *
	 * @param UrlPath|string $path 行先。
	 * @param UrlQuery|null $query 付与するクエリ。
	 * @return RedirectActionResult
	 */
	protected function redirectPath(UrlPath|string $path, ?UrlQuery $query = null): RedirectActionResult
	{
		$url = $this->stores->special->getServerUrl();

		if (is_string($path)) {
			$path = new UrlPath($path);
		}
		$url = $url->changePath($path);

		if ($query !== null) {
			$url = $url->changeQuery($query);
		}

		return $this->redirectUrl($url);
	}

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
	 * @return ViewActionResult
	 */
	protected function createViewActionResult(
		string $templateBaseName,
		string $actionName,
		TemplateParameter $templateParameter,
		array $headers,
		ITemplateFactory $templateFactory,
		IUrlHelper $urlHelper
	): ViewActionResult {
		return new ViewActionResult($templateBaseName, $actionName, $templateParameter, $headers, $templateFactory, $urlHelper);
	}

	/**
	 * Viewを表示。
	 *
	 * @param string $controllerName コントローラ完全名。
	 * @phpstan-param class-string<ControllerBase> $controllerName
	 * @param string $action アクション名。
	 * @param TemplateParameter $parameter View連携データ。
	 * @return ViewActionResult
	 */
	protected function viewWithController(string $controllerName, string $action, TemplateParameter $parameter): ViewActionResult
	{
		$lastWord = 'Controller';

		$skipBaseName = $this->getSkipBaseName();
		$index = Text::getPosition($controllerName, $skipBaseName);
		$length = Text::getLength($skipBaseName);

		$controllerClassName = Text::substring($controllerName, $index + $length + 1);
		$controllerBaseName = Text::substring($controllerClassName, 0, Text::getLength($controllerClassName) - Text::getLength($lastWord));

		$templateDirPath = Text::replace($controllerBaseName, '\\', DIRECTORY_SEPARATOR);

		return $this->createViewActionResult($templateDirPath, $action, $parameter, $this->getResponseHeaders(), $this->templateFactory, $this->urlHelper);
	}

	/**
	 * Viewを表示
	 *
	 * @param string $action アクション名
	 * @param TemplateParameter $parameter View連携データ。
	 * @return ViewActionResult
	 */
	protected function view(string $action, TemplateParameter $parameter): ViewActionResult
	{
		$className = get_class($this);

		return $this->viewWithController($className, $action, $parameter);
	}

	/**
	 * データ応答。
	 *
	 * @param DataContent $content
	 * @return DataActionResult
	 */
	protected function data(DataContent $content): DataActionResult
	{
		return new DataActionResult($content);
	}

	#endregion
}
