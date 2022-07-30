<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\Code;
use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\Logging;
use PeServer\Core\Mvc\ControllerArgument;
use PeServer\Core\Mvc\DataContent;
use PeServer\Core\Mvc\LogicBase;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Mvc\Result\DataActionResult;
use PeServer\Core\Mvc\Result\RedirectActionResult;
use PeServer\Core\Mvc\Result\ViewActionResult;
use PeServer\Core\Mvc\TemplateParameter;
use PeServer\Core\ReflectionUtility;
use PeServer\Core\Store\Stores;
use PeServer\Core\StringUtility;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\Type;
use PeServer\Core\UrlUtility;


/**
 * コントローラ基底処理。
 */
abstract class ControllerBase
{
	/**
	 * ロガー。
	 * @readonly
	 */
	protected ILogger $logger;

	/** @readonly */
	protected Stores $stores;

	/** コントローラ内で今輝いてるロジック。よくないんよなぁ。 */
	protected ?LogicBase $logic = null;

	/**
	 * 生成。
	 *
	 * @param ControllerArgument $argument コントローラ入力値。
	 */
	protected function __construct(ControllerArgument $argument)
	{
		$this->stores = $argument->stores;
		$this->logger = $argument->logger;
	}

	/**
	 * コントローラ完全名からコントローラベース名を取得する際にスキップする文言(文字列長が使用される)
	 *
	 * @return string
	 */
	protected abstract function getSkipBaseName(): string;

	/**
	 * ロジック用パラメータ生成処理。
	 *
	 * @param string $logicName ロジック名
	 * @phpstan-param class-string<LogicBase> $logicName
	 * @param HttpRequest $request リクエストデータ
	 * @return LogicParameter
	 */
	protected function createParameter(string $logicName, HttpRequest $request): LogicParameter
	{
		return new LogicParameter(
			$request,
			$this->stores,
			Logging::create($logicName)
		);
	}

	/**
	 * ロジック生成処理。
	 *
	 * @param string $logicClass ロジック完全名。
	 * @phpstan-param class-string<LogicBase> $logicClass
	 * @param HttpRequest $request リクエストデータ
	 * @return LogicBase
	 */
	protected function createLogic(string $logicClass, HttpRequest $request, mixed ...$parameters): LogicBase
	{
		if (!is_null($this->logic)) {
			throw new InvalidOperationException();
		}

		$parameter = $this->createParameter($logicClass, $request);
		/** @var LogicBase */
		$logic = ReflectionUtility::create($logicClass, LogicBase::class, $parameter, ...$parameters);
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

		if (!is_null($this->logic)) {
			$headers = $this->logic->getResponseHeaders();
		}

		return $headers;
	}

	/**
	 * URLリダイレクト。
	 *
	 * @param string $url
	 * @return RedirectActionResult
	 */
	public function redirectUrl(string $url): RedirectActionResult
	{
		return new RedirectActionResult($url, HttpStatus::found());
	}

	/**
	 * ドメイン内でリダイレクト。
	 * 基本的にこれを使っておけばいい。
	 *
	 * @param string $path 行先。
	 * @param array<non-empty-string,string>|null $query 付与するクエリ。
	 * @return RedirectActionResult
	 */
	public function redirectPath(string $path, ?array $query = null): RedirectActionResult
	{
		$url = UrlUtility::buildPath($path, $query ?? [], $this->stores->special);
		return $this->redirectUrl($url);
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
		$index = StringUtility::getPosition($controllerName, $skipBaseName);
		$length = StringUtility::getLength($skipBaseName);

		$controllerClassName = StringUtility::substring($controllerName, $index + $length + 1);
		$controllerBaseName = StringUtility::substring($controllerClassName, 0, StringUtility::getLength($controllerClassName) - StringUtility::getLength($lastWord));

		$templateDirPath = StringUtility::replace($controllerBaseName, '\\', DIRECTORY_SEPARATOR);

		return new ViewActionResult($templateDirPath, $action, $parameter, $this->getResponseHeaders());
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
}
