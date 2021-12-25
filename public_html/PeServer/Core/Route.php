<?php

declare(strict_types=1);

namespace PeServer\Core;

use \LogicException;

/**
 * ルーティング情報
 */
class Route
{
	public const DEFAULT_METHOD = null;

	/**
	 * ベースパス。
	 *
	 * @var string
	 */
	private $_basePath;
	/**
	 * クラス完全名。
	 *
	 * @var string
	 */
	private $_className;
	/**
	 * アクション一覧。
	 *
	 * @var array<string,Action>
	 */
	private $_actions = array();

	private ActionOptions $_baseOptions;

	/**
	 * ルーティング情報にコントローラを登録
	 *
	 * @param string $path URLとしてのパス。先頭が api 以外の場合に index アクションが自動登録される
	 * @param string $className 使用されるクラス完全名
	 * @param ActionOptions|null $options ベースとなるオプション設定。nullの場合は ActionOptions::none() が使用される。
	 */
	public function __construct(string $path, string $className, ?ActionOptions $options = null)
	{
		// if(str_starts_with($path, '/')) {
		// 	die();
		// }
		// if(str_ends_with($path, '/')) {
		// 	die();
		// }

		if (StringUtility::isNullOrEmpty($path)) {
			$this->_basePath = $path;
		} else {
			$trimPath = trim($path);
			if ($trimPath !== trim($trimPath, '/')) {
				throw new LogicException('path start or end -> /');
			}
			$this->_basePath = $trimPath;
		}

		$this->_baseOptions = $options ?? ActionOptions::none();

		$this->_className = $className;
		if (mb_substr($this->_basePath, 0, 3) != 'api') {
			$this->addAction('', HttpMethod::get(), 'index', $options);
		}
	}

	/**
	 * アクション設定。
	 *
	 * @param string $actionName URLとして使用されるパス, パス先頭が : でURLパラメータとなり、パラメータ名の @ 以降は一致正規表現となる。
	 * @param HttpMethod $httpMethod 使用するHTTPメソッド。
	 * @param string|null $methodName 呼び出されるコントローラメソッド。未指定なら $actionName が使用される。
	 * @param ActionOptions|null $options オプション設定。nullの場合はコンストラクタで渡されたオプションが使用される。
	 * @return Route
	 */
	public function addAction(string $actionName, HttpMethod $httpMethod, ?string $methodName = null, ?ActionOptions $options = null): Route
	{
		if (!isset($this->_actions[$actionName])) {
			$this->_actions[$actionName] = new Action();
		}
		$this->_actions[$actionName]->add(
			$httpMethod,
			StringUtility::isNullOrWhiteSpace($methodName) ? $actionName : $methodName,
			$options ?? $this->_baseOptions
		);

		return $this;
	}

	/**
	 * Undocumented function
	 *
	 * @param string $httpMethod
	 * @param Action $action
	 * @param array<string,string> $urlParameters
	 * @return array{code:int,class:string,method:string,params:array<string,string>,options:ActionOptions}
	 */
	private function getActionCore(string $httpMethod, Action $action, array $urlParameters): array
	{
		$actionValues = $action->get($httpMethod);
		if (is_null($actionValues)) {
			return [
				'code' => HttpStatusCode::METHOD_NOT_ALLOWED,
				'class' => $this->_className,
				'method' => '',
				'params' => $urlParameters,
				'options' => ActionOptions::none(),
			];
		}

		return [
			'code' => HttpStatusCode::DO_EXECUTE,
			'class' => $this->_className,
			'method' => $actionValues['method'],
			'params' => $urlParameters,
			'options' => $actionValues['options'],
		];
	}

	/**
	 * メソッド・リクエストパスから登録されているアクションを取得。
	 *
	 * @param string $httpMethod HttpMethod を参照のこと
	 * @param string[] $requestPaths リクエストパス。URLパラメータは含まない
	 * @return array{code:int,class:string,method:string,params:array<string,string>,options:ActionOptions} 存在する場合にクラス・メソッドのペア。存在しない場合は null
	 */
	public function getAction(string $httpMethod, array $requestPaths): ?array
	{
		$requestPath = implode('/', $requestPaths);

		if (!StringUtility::startsWith($requestPath, $this->_basePath, false)) {
			return [
				'code' => HttpStatusCode::NOT_FOUND,
				'class' => $this->_className,
				'method' => '',
				'params' => [],
				'options' => ActionOptions::none(),
			];
		}

		//$actionPath = $requestPaths[count($requestPaths) - 1];
		$actionPath = ltrim(mb_substr($requestPath, mb_strlen($this->_basePath)), '/');
		$actionPaths = explode('/', $actionPath);

		if (!isset($this->_actions[$actionPath])) {
			// URLパラメータチェック
			foreach ($this->_actions as $key => $action) {
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
				'class' => $this->_className,
				'method' => '',
				'params' => [],
				'options' => ActionOptions::none(),
			];
		}

		return $this->getActionCore($httpMethod, $this->_actions[$actionPath], []);
	}
}
