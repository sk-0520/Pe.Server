<?php

declare(strict_types=1);

namespace PeServer\Core;

use \LogicException;
use Prophecy\Util\StringUtil;

/**
 * ルーティング情報
 */
class Route
{
	/**
	 * ベースパス。
	 *
	 * @var string
	 */
	private $basePath;
	/**
	 * クラス完全名。
	 *
	 * @var string
	 */
	private $className;
	/**
	 * アクション一覧。
	 *
	 * @var array<string,Action>
	 */
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
			$this->addAction('', HttpMethod::get(), 'index');
		}
	}

	/**
	 * アクション設定
	 *
	 * TODO: メソッド違う同一パスが対応できていない
	 *
	 * @param string $actionName URLとして使用されるパス, パス先頭が : でURLパラメータとなる
	 * @param HttpMethod $httpMethod 使用するHTTPメソッド
	 * @param string|null $methodName 呼び出されるコントローラメソッド。未指定なら $actionName が使用される
	 * @return Route
	 */
	public function addAction(string $actionName, HttpMethod $httpMethod, ?string $methodName = null): Route
	{
		if (!isset($this->actions[$actionName])) {
			$this->actions[$actionName] = new Action();
		}
		$this->actions[$actionName]->add(
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
	 * @return array{code:int,class:string,method:string} 存在する場合にクラス・メソッドのペア。存在しない場合は null
	 */
	public function getAction(string $httpMethod, array $requestPaths): ?array
	{
		$requestPath = implode('/', $requestPaths);

		if (!StringUtility::startsWith($requestPath, $this->basePath, false)) {
			return [
				'code' => HttpStatusCode::NOT_FOUND,
				'class' => $this->className,
				'method' => '',
			];
		}

		$actionPath = $requestPaths[count($requestPaths) - 1];

		if (!isset($this->actions[$actionPath])) {
			return [
				'code' => HttpStatusCode::NOT_FOUND,
				'class' => $this->className,
				'method' => '',
			];
		}

		$action = $this->actions[$actionPath];
		$callMethod = $action->get($httpMethod);
		if (StringUtility::isNullOrEmpty($callMethod)) {
			return [
				'code' => HttpStatusCode::METHOD_NOT_ALLOWED,
				'class' => $this->className,
				'method' => '',
			];
		}

		// @phpstan-ignore-next-line
		return [
			'code' => HttpStatusCode::DO_EXECUTE,
			'class' => $this->className,
			'method' => $callMethod,
		];
	}
}
