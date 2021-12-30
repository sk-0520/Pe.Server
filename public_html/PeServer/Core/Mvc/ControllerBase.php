<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use \LogicException;
use PeServer\Core\ArrayUtility;
use PeServer\Core\HttpStatus;
use \PeServer\Core\ILogger;
use \PeServer\Core\Log\Logging;
use \PeServer\Core\Mvc\Template;
use \PeServer\Core\Mvc\LogicBase;
use \PeServer\Core\StringUtility;
use \PeServer\Core\ResponseOutput;
use \PeServer\Core\Mvc\ActionRequest;
use \PeServer\Core\Store\CookieStore;
use \PeServer\Core\Mvc\ActionResponse;
use \PeServer\Core\Mvc\LogicParameter;
use \PeServer\Core\Store\SessionStore;
use PeServer\Core\Mvc\ViewActionResult;
use \PeServer\Core\Mvc\SessionNextState;
use \PeServer\Core\Mvc\TemplateParameter;
use \PeServer\Core\Mvc\ControllerArgument;
use \PeServer\Core\Throws\InvalidOperationException;



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
	protected SessionStore $session;

	protected ?LogicBase $logic = null;

	protected function __construct(ControllerArgument $argument)
	{
		$this->logger = $argument->logger;
		$this->cookie = $argument->cookie;
		$this->session = $argument->session;

		$this->logger->trace('CONTROLLER');
	}

	/**
	 * ロジック用パラメータ生成処理。
	 *
	 * @param string $logicName ロジック名
	 * @param ActionRequest $request リクエストデータ
	 * @return LogicParameter
	 */
	protected function createParameter(string $logicName, ActionRequest $request): LogicParameter
	{
		return new LogicParameter(
			$request,
			$this->cookie,
			$this->session,
			Logging::create($logicName)
		);
	}

	/**
	 * ロジック生成処理。
	 *
	 * @param string $logicClass ロジック完全名。
	 * @param ActionRequest $request リクエストデータ
	 * @return LogicBase
	 */
	protected function createLogic(string $logicClass, ActionRequest $request): LogicBase
	{
		if (!is_null($this->logic)) {
			throw new InvalidOperationException();
		}

		$parameter = $this->createParameter($logicClass, $request);
		/** @var LogicBase */
		$logic = new $logicClass($parameter);
		$this->logic = $logic;
		return $logic;
	}

	public function existsResult(LogicBase $logic, string $key): bool
	{
		return isset($logic->result[$key]);
	}

	/**
	 * Undocumented function
	 *
	 * @param LogicBase $logic
	 * @param string $key
	 * @param mixed $value
	 * @return boolean
	 */
	public function hasResult(LogicBase $logic, string $key, $value): bool
	{
		if ($this->existsResult($logic, $key)) {
			return $logic->result[$key] === $value;
		}

		return false;
	}

	/**
	 * ロジック側で指定されたセッションステータスに従ってセッション情報を設定。
	 *
	 * @return void
	 * @throws InvalidOperationException ロジックが生成されていない。
	 */
	private function applySession(): void
	{
		if (is_null($this->logic)) {
			throw new InvalidOperationException();
		}

		$nextState = $this->logic->sessionNextState();
		switch ($nextState) {
			case SessionNextState::NORMAL:
				if ($this->session->isChanged()) {
					if (!$this->session->isStarted()) {
						$this->session->start();
					}
					$this->session->apply();
				}
				break;
			case SessionNextState::CANCEL:
				// なんもしない
				break;
			case SessionNextState::RESTART:
				if ($this->session->isStarted()) {
					$this->session->restart();
				} else {
					$this->session->start();
				}
				$this->session->apply();
				break;
			case SessionNextState::SHUTDOWN:
				if ($this->session->isStarted()) {
					$this->session->shutdown();
				}
				break;

			default:
				throw new LogicException();
		}
	}

	private function applyStore(): void
	{
		$this->applySession();
		$this->cookie->apply();
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
		$httpProtocol = StringUtility::isNullOrEmpty(ArrayUtility::getOr($_SERVER, 'HTTPS', '')) ? 'http://' : 'https://';
		return $this->redirectUrl($httpProtocol . $_SERVER['SERVER_NAME'] . '/' .  ltrim($path, '/'));
	}

	/**
	 * Viewを表示。
	 *
	 * @param string $controllerName コントローラ完全名。
	 * @param string $action アクション名。
	 * @param TemplateParameter $parameter View連携データ。
	 * @return ViewActionResult
	 */
	public function viewWithController(string $controllerName, string $action, TemplateParameter $parameter): ViewActionResult
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
	public function view(string $action, TemplateParameter $parameter): ViewActionResult
	{
		$className = get_class($this);

		return $this->viewWithController($className, $action, $parameter);
	}

	/**
	 * データ応答。
	 *
	 * @param ActionResponse $response 応答データ。
	 * @return DataActionResult
	 */
	public function data(ActionResponse $response): DataActionResult
	{
		return new DataActionResult($response, $this->getResponseHeaders());
	}

	/**
	 * アクション結果操作の実行。
	 *
	 * @param IActionResult $result
	 * @return void
	 */
	public function execute(IActionResult $result): void
	{
		if (!is_null($this->logic)) {
			$this->applyStore();
		}

		$result->execute();
	}
}
