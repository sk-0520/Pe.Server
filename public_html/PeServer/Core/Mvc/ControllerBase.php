<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\Bytes;
use PeServer\Core\ILogger;
use PeServer\Core\UrlUtility;
use PeServer\Core\Log\Logging;
use PeServer\Core\Mvc\LogicBase;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Store\CookieStore;
use PeServer\Core\Mvc\ActionResponse;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Store\SessionStore;
use PeServer\Core\Store\TemporaryStore;
use PeServer\Core\Mvc\TemplateParameter;
use PeServer\Core\Mvc\ControllerArgument;
use PeServer\Core\Mvc\Result\DataActionResult;
use PeServer\Core\Mvc\Result\ViewActionResult;
use PeServer\Core\Mvc\Result\RedirectActionResult;
use PeServer\Core\Throws\InvalidOperationException;


/**
 * コントローラ基底処理。
 */
abstract class ControllerBase
{
	/**
	 * ロガー。
	 *
	 * @var ILogger
	 */
	protected $logger;
	/**
	 * コントローラ完全名からコントローラベース名を取得する際にスキップする文言(文字列長が使用される)
	 * このアプリケーション内に閉じる場合は基本的に変更不要だが、別アプリケーションに持ち運ぶ場合などはここを変更する必要あり(継承側で書き換える想定)。
	 *
	 * @var string
	 */
	protected $skipBaseName = 'PeServer\\App\\Controllers\\Page';

	protected CookieStore $cookie;
	protected TemporaryStore $temporary;
	protected SessionStore $session;

	protected ?LogicBase $logic = null;

	protected function __construct(ControllerArgument $argument)
	{
		$this->logger = $argument->logger;
		$this->cookie = $argument->cookie;
		$this->temporary = $argument->temporary;
		$this->session = $argument->session;
	}

	/**
	 * ロジック用パラメータ生成処理。
	 *
	 * @param string $logicName ロジック名
	 * @param HttpRequest $request リクエストデータ
	 * @return LogicParameter
	 */
	protected function createParameter(string $logicName, HttpRequest $request): LogicParameter
	{
		return new LogicParameter(
			$request,
			$this->cookie,
			$this->temporary,
			$this->session,
			Logging::create($logicName)
		);
	}

	/**
	 * ロジック生成処理。
	 *
	 * @param string $logicClass ロジック完全名。
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
		$logic = new $logicClass($parameter, ...$parameters);
		$this->logic = $logic;
		return $logic;
	}

	/**
	 * ロジック側で生成された応答ヘッダを取得。
	 *
	 * @return array<string,string[]> 応答ヘッダ。ロジック未生成の場合は空の応答ヘッダを返す。
	 */
	private function getResponseHeaders(): array
	{
		/** @var array<string,string[]> */
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
	 *
	 * @param string $path
	 * @param array<string,string>|null $query
	 * @return RedirectActionResult
	 */
	public function redirectPath(string $path, ?array $query = null): RedirectActionResult
	{
		$url = UrlUtility::buildPath($path, $query ?? []);
		return $this->redirectUrl($url);
	}

	/**
	 * Viewを表示。
	 *
	 * @param string $controllerName コントローラ完全名。
	 * @param string $action アクション名。
	 * @param TemplateParameter $parameter View連携データ。
	 * @return ViewActionResult
	 */
	protected function viewWithController(string $controllerName, string $action, TemplateParameter $parameter): ViewActionResult
	{
		$lastWord = 'Controller';
		$controllerClassName = mb_substr($controllerName, mb_strpos($controllerName, $this->skipBaseName) + mb_strlen($this->skipBaseName) + 1);
		$controllerBaseName = mb_substr($controllerClassName, 0, mb_strlen($controllerClassName) - mb_strlen($lastWord));

		$templateDirPath = str_replace('\\', DIRECTORY_SEPARATOR, $controllerBaseName);

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
