<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use \LogicException;
use \Smarty;
use \PeServer\Core\ILogger;
use \PeServer\Core\ActionOptions;
use \PeServer\Core\ActionRequest;
use \PeServer\Core\ActionResponse;
use \PeServer\Core\ResponseOutput;
use \PeServer\Core\HttpStatus;
use \PeServer\Core\ArrayUtility;
use \PeServer\Core\Mvc\ControllerArguments;
use \PeServer\Core\Mvc\Template;
use \PeServer\Core\Mvc\LogicBase;
use \PeServer\Core\Mvc\LogicParameter;
use \PeServer\Core\Mvc\SessionNextState;
use \PeServer\Core\Mvc\SessionStore;
use \PeServer\Core\Log\Logging;
use \PeServer\Core\StringUtility;
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

	protected SessionStore $session;

	protected ?LogicBase $logic = null;

	protected function __construct(ControllerArguments $arguments)
	{
		$this->logger = $arguments->logger;
		$this->session = $arguments->session;

		$this->logger->trace('CONTROLLER');
	}

	/**
	 * Undocumented function
	 *
	 * @param string $url
	 * @return no-return
	 */
	public function redirectUrl(string $url): void
	{
		$this->logger->info('リダイレクト: {0}', $url);
		header("Location: $url");
		exit;
	}

	/**
	 * ドメイン内でリダイレクト。
	 *
	 * @param string $path
	 * @param array<string,string>|null $query
	 * @return no-return
	 */
	public function redirectPath(string $path, ?array $query = null): void
	{
		$this->applySession();

		$httpProtocol = StringUtility::isNullOrEmpty($_SERVER['HTTPS']) ? 'http://' : 'https://';
		$this->redirectUrl($httpProtocol . $_SERVER['SERVER_NAME'] . '/' .  ltrim($path, '/'));
	}

	/**
	 * ロジック用パラメータ生成処理。
	 *
	 * @param string $logicName ロジック名
	 * @param ActionRequest $request リクエストデータ
	 * @return LogicParameter
	 */
	protected function createParameter(string $logicName, ActionRequest $request, ActionOptions $options): LogicParameter
	{
		return new LogicParameter(
			$request,
			$this->session,
			$options,
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
	protected function createLogic(string $logicClass, ActionRequest $request, ActionOptions $options): LogicBase
	{
		if (!is_null($this->logic)) {
			throw new InvalidOperationException();
		}

		$parameter = $this->createParameter($logicClass, $request, $options);
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

	/**
	 * Viewを表示。
	 *
	 * @param string $controllerName コントローラ完全名。
	 * @param string $action アクション名。
	 * @param HttpStatus $httpStatus HTTPステータスコード。
	 * @param array<string|int,string|int|array<mixed>> $parameters View連携データ。
	 * @return void
	 */
	public function viewWithController(string $controllerName, string $action, HttpStatus $httpStatus, array $parameters = array())
	{
		$lastWord = 'Controller';
		$controllerClassName = mb_substr($controllerName, mb_strpos($controllerName, $this->skipBaseName) + mb_strlen($this->skipBaseName) + 1);
		$controllerBaseName = mb_substr($controllerClassName, 0, mb_strlen($controllerClassName) - mb_strlen($lastWord));

		$templateDirPath = str_replace('\\', DIRECTORY_SEPARATOR, $controllerBaseName);

		$template = Template::create($templateDirPath);

		$this->applySession();

		$template->show("$action.tpl", $parameters);
	}

	/**
	 * Viewを表示
	 *
	 * @param string $action アクション名
	 * @param array<string|int,string|int|HttpStatus|array<mixed>> $parameters View連携データ。
	 * @return void
	 */
	public function view(string $action, array $parameters = array()): void
	{
		$className = get_class($this);

		$httpStatus = ArrayUtility::getOr($parameters, 'status', HttpStatus::ok());
		unset($parameters['status']);

		$this->viewWithController($className, $action, $httpStatus, $parameters); // @phpstan-ignore-line アプリ側とコア側で都合が混在している(HttpStatusが完全に💩)
	}

	/**
	 * データ応答。
	 *
	 * @param ActionResponse $response 応答データ。
	 * @return void
	 */
	public function data(ActionResponse $response): void
	{
		$this->applySession();

		header('Content-Type: ' . $response->mime);
		if ($response->chunked) {
			header("Transfer-encoding: chunked");
		}

		if (is_null($response->callback)) {
			$converter = new ResponseOutput();
			$converter->output($response->mime, $response->chunked, $response->data);
		} else {
			call_user_func_array($response->callback, [$response->mime, $response->chunked, $response->data]);
		}
	}
}
