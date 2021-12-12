<?php

declare(strict_types=1);

namespace PeServer\Core;

use LogicException;

/**
 * ルーティング情報
 */
class Route
{
	private $basePath;
	private $className;
	private $actions = array();

	/**
	 * ルーティング情報にコントローラを登録
	 *
	 * @param string $path URLとしてのパス。先頭が api 以外の場合に index アクションが自動登録される
	 * @param string $className 使用されるクラス完全名
	 */
	public function __construct(string $path, string $className)
	{
		// if(str_starts_with($path, '/')) {
		// 	die();
		// }
		// if(str_ends_with($path, '/')) {
		// 	die();
		// }

		if (StringUtility::isNullOrEmpty($path)) {
			$this->basePath = $path;
		} else {
			$trimPath = trim($path);
			if ($trimPath !== trim($trimPath, '/')) {
				throw new LogicException('path start or end -> /');
			}
			$this->basePath = $trimPath;
		}

		$this->className = $className;
		if (mb_substr($this->basePath, 0, 3) != 'api') {
			$this->actions[''] = new Action(HttpMethod::ALL, 'index');
		}
	}

	/**
	 * アクション設定
	 *
	 * @param string $httpMethod 使用するHTTPメソッド: HttpMethod を参照
	 * @param string $actionName URLとして使用されるパス
	 * @param string|null $methodName 呼び出されるコントローラメソッド。未指定なら $actionName が使用される
	 * @return Route
	 */
	public function action(string $httpMethod, string $actionName, ?string $methodName = null): Route
	{
		$this->actions[$actionName] = new Action(
			$httpMethod,
			StringUtility::isNullOrWhiteSpace($methodName) ? $actionName : $methodName
		);
		return $this;
	}

	/**
	 * メソッド・リクエストパスから登録されているアクションを取得。
	 *
	 * @param string $httpMethod HttpMethod を参照のこと
	 * @param string[] $requestPaths リクエストパス。URLパラメータは含まない
	 * @return array{class:string,method:string}|null 存在する場合にクラス・メソッドのペア。存在しない場合は null
	 */
	public function getAction(string $httpMethod, array $requestPaths)
	{
		$requestPath = implode('/', $requestPaths);

		if (!StringUtility::startsWith($requestPath, $this->basePath, false)) {
			return null;
		}

		$actionPath = $requestPaths[count($requestPaths) - 1];

		if (!isset($this->actions[$actionPath])) {
			return null;
		}

		$action = $this->actions[$actionPath];
		//TODO: HTTPメソッド判定

		return [
			'class' => $this->className,
			'method' => $action->callMethod,
		];
	}
}
