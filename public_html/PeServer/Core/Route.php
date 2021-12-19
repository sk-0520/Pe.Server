<?php

declare(strict_types=1);

namespace PeServer\Core;

use \LogicException;

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
	 * @param string $actionName URLとして使用されるパス, パス先頭が : でURLパラメータとなり、パラメータ名の @ 以降は一致正規表現となる
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
	 * Undocumented function
	 *
	 * @param string $httpMethod
	 * @param Action $action
	 * @param array<string,string> $urlParameters
	 * @return array{code:int,class:string,method:string,params:array<string,string>}
	 */
	private function getActionCore(string $httpMethod, Action $action, array $urlParameters): array
	{
		$callMethod = $action->get($httpMethod);
		if (StringUtility::isNullOrEmpty($callMethod)) {
			return [
				'code' => HttpStatusCode::METHOD_NOT_ALLOWED,
				'class' => $this->className,
				'method' => '',
				'params' => $urlParameters,
			];
		}

		// @phpstan-ignore-next-line
		return [
			'code' => HttpStatusCode::DO_EXECUTE,
			'class' => $this->className,
			'method' => $callMethod,
			'params' => $urlParameters,
		];
	}

	/**
	 * メソッド・リクエストパスから登録されているアクションを取得。
	 *
	 * @param string $httpMethod HttpMethod を参照のこと
	 * @param string[] $requestPaths リクエストパス。URLパラメータは含まない
	 * @return array{code:int,class:string,method:string,params:array<string,string>} 存在する場合にクラス・メソッドのペア。存在しない場合は null
	 */
	public function getAction(string $httpMethod, array $requestPaths): ?array
	{
		$requestPath = implode('/', $requestPaths);

		if (!StringUtility::startsWith($requestPath, $this->basePath, false)) {
			return [
				'code' => HttpStatusCode::NOT_FOUND,
				'class' => $this->className,
				'method' => '',
				'params' => [],
			];
		}

		//$actionPath = $requestPaths[count($requestPaths) - 1];
		$actionPath = ltrim(mb_substr($requestPath, mb_strlen($this->basePath)), '/');
		$actionPaths = explode('/', $actionPath);

		if (!isset($this->actions[$actionPath])) {
			// URLパラメータチェック
			foreach ($this->actions as $key => $action) {
				// 定義内にURLパラメータが無ければ破棄
				if (!StringUtility::contains($key, ':', false)) {
					continue;
				}

				$keyPaths = explode('/', $key);
				if (count($keyPaths) !== count($actionPaths)) {
					continue;
				}

				$calcPaths = array_filter(array_map(function ($i, $value) use ($actionPaths) {
					$length = StringUtility::getLength($value);
					if ($length === 0 || $value[0] !== ':') {
						return ['key' => $value, 'name' => '', 'value' => $actionPaths[$i]];
					}
					$splitPaths = explode('@', $value, 2);
					$requestKey = StringUtility::substring($splitPaths[0], 1);
					$isRegex = 1 < count($splitPaths);
					if ($isRegex) {
						$pattern = "/$splitPaths[1]/";
						if (preg_match($pattern, $actionPaths[$i])) {
							return ['key' => $value, 'name' => $requestKey, 'value' => $actionPaths[$i]];
						}
						return null;
					} else {
						return ['key' => $value, 'name' => $requestKey, 'value' => $actionPaths[$i]];
					}
				}, array_keys($keyPaths), array_values($keyPaths)), function ($i) {
					return !is_null($i);
				});

				// 非URLパラメータ項目は一致するか
				if (count($calcPaths) !== count($actionPaths)) {
					continue;
				}
				$success = true;
				for ($i = 0; $i < count($calcPaths) && $success; $i++) {
					$calcPath = $calcPaths[$i];
					if (StringUtility::isNullOrEmpty($calcPath['name'])) {
						$success = $calcPath['key'] === $actionPaths[$i];
					}
				}
				if (!$success) {
					continue;
				}

				$calcKey = implode('/', array_column($calcPaths, 'key'));
				if ($key !== $calcKey) {
					continue;
				}

				$calcParameters = array_filter($calcPaths, function ($i) {
					return !StringUtility::isNullOrEmpty($i['name']);
				});
				$flatParameters = [];
				foreach ($calcParameters as $calcParameter) {
					$flatParameters[$calcParameter['name']] = $calcParameter['value'];
				}

				$result = $this->getActionCore($httpMethod, $action, $flatParameters);
				if ($result['code'] === HttpStatusCode::DO_EXECUTE) {
					return $result;
				}
			}

			return [
				'code' => HttpStatusCode::NOT_FOUND,
				'class' => $this->className,
				'method' => '',
				'params' => [],
			];
		}

		return $this->getActionCore($httpMethod, $this->actions[$actionPath], []);
	}
}
