<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use \LogicException;
use \Smarty;
use \PeServer\Core\ILogger;
use \PeServer\Core\ActionRequest;
use \PeServer\Core\ActionResponse;
use \PeServer\Core\ResponseOutput;
use \PeServer\Core\HttpStatusCode;
use \PeServer\Core\ArrayUtility;
use \PeServer\Core\Mvc\ControllerArguments;
use \PeServer\Core\Mvc\Template;
use \PeServer\Core\Mvc\LogicBase;
use \PeServer\Core\Mvc\LogicParameter;
use \PeServer\Core\Log\Logging;

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
	protected $skipBaseName = 'PeServer\\App\\Controllers';

	public function __construct(ControllerArguments $arguments)
	{
		$this->logger = $arguments->logger;

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
		$parameter = $this->createParameter($logicClass, $request);
		// @phpstan-ignore-next-line
		return new $logicClass($parameter);
	}

	/**
	 * テンプレート生成。
	 *
	 * @param string $baseName コントローラ名。
	 * @return Smarty 本処理では Smarty を使用するが将来変わる可能性あり。
	 */
	protected function createTemplate(string $baseName): Smarty // @phpstan-ignore-line
	{
		return Template::createTemplate($baseName);
	}

	/**
	 * Viewを表示。
	 *
	 * @param string $controllerName コントローラ完全名。
	 * @param string $action アクション名。
	 * @param integer $httpStatusCode HTTPステータスコード。
	 * @param array|null $parameters View連携データ。
	 * @return void
	 */
	public function viewWithController(string $controllerName, string $action, int $httpStatusCode, ?array $parameters = null) // @phpstan-ignore-line
	{
		$lastWord = 'Controller';
		$controllerClassName = mb_substr($controllerName, mb_strpos($controllerName, $this->skipBaseName) + mb_strlen($this->skipBaseName) + 1);
		$controllerBaseName = mb_substr($controllerClassName, 0, mb_strlen($controllerClassName) - mb_strlen($lastWord));

		$templateDirPath = str_replace('\\', DIRECTORY_SEPARATOR, $controllerBaseName);
		$smarty = $this->createTemplate($templateDirPath);

		$smarty->assign($parameters); // @phpstan-ignore-line
		$smarty->display("$action.tpl"); // @phpstan-ignore-line
	}

	/**
	 * Viewを表示
	 *
	 * @param string $action アクション名
	 * @param array|null $parameters View連携データ。
	 * @return void
	 */
	public function view(string $action, ?array $parameters = null): void // @phpstan-ignore-line
	{
		$className = get_class($this);

		$httpStatusCode = ArrayUtility::getOr($parameters, 'status', HttpStatusCode::OK);

		$this->viewWithController($className, $action, $httpStatusCode, $parameters);
	}

	/**
	 * データ応答。
	 *
	 * @param ActionResponse $response 応答データ。
	 * @return void
	 */
	public function data(ActionResponse $response): void
	{
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
