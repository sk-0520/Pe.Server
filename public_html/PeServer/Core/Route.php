<?php

declare(strict_types=1);

namespace PeServer\Core;

use \LogicException;
use PeServer\Core\Throws\ArgumentException;

/**
 * ルーティング情報
 */
class Route
{
	public const DEFAULT_METHOD = null;
	public const CLEAR_MIDDLEWARE = '*';

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
	 * Undocumented variable
	 *
	 * @var array<IMiddleware|string>
	 */
	private array $baseMiddleware;

	/**
	 * ルーティング情報にコントローラを登録
	 *
	 * @param string $path URLとしてのパス。先頭が api/ajax 以外の場合に index アクションが自動登録される
	 * @param string $className 使用されるクラス完全名
	 * @param array<IMiddleware|string> $middleware ベースとなるミドルウェア。
	 */
	public function __construct(string $path, string $className, array $middleware = [])
	{
		if (StringUtility::isNullOrEmpty($path)) {
			$this->basePath = $path;
		} else {
			$trimPath = StringUtility::trim($path);
			if ($trimPath !== StringUtility::trim($trimPath, '/')) {
				throw new LogicException('path start or end -> /');
			}
			$this->basePath = $trimPath;
		}

		if (ArrayUtility::contains($middleware, self::CLEAR_MIDDLEWARE)) {
			throw new ArgumentException('$middleware');
		}

		$this->baseMiddleware = $middleware;
		$this->className = $className;

		if (!(StringUtility::startsWith($this->basePath, 'api', false) || StringUtility::startsWith($this->basePath, 'ajax', false))) {
			$this->addAction('', HttpMethod::get(), 'index', $this->baseMiddleware);
		}
	}

	/**
	 * アクション設定。
	 *
	 * @param string $actionName URLとして使用されるパス, パス先頭が : でURLパラメータとなり、パラメータ名の @ 以降は一致正規表現となる。
	 * @param HttpMethod $httpMethod 使用するHTTPメソッド。
	 * @param string|null $methodName 呼び出されるコントローラメソッド。未指定なら $actionName が使用される。
	 * @param array<IMiddleware|string>|null $middleware 専用ミドルウェア。 第一要素が CLEAR_MIDDLEWARE であれば既存のミドルウェアを破棄する。nullの場合はコンストラクタで渡されたミドルウェアが使用される。
	 * @return Route
	 */
	public function addAction(string $actionName, HttpMethod $httpMethod, ?string $methodName = null, ?array $middleware = null): Route
	{
		if (!isset($this->actions[$actionName])) {
			$this->actions[$actionName] = new Action();
		}

		$customMiddleware = null;
		if (ArrayUtility::getCount($middleware)) {
			$customMiddleware = [];
			foreach ($middleware as $index => $mw) { // @phpstan-ignore-line ArrayUtility::getCount
				if ($mw === self::CLEAR_MIDDLEWARE) {
					if ($index) {
						throw new ArgumentException();
					} else {
						$customMiddleware = array_merge($customMiddleware, $this->baseMiddleware);
					}
				} else {
					$customMiddleware[] = $mw;
				}
			}
		} else {
			$customMiddleware = $this->baseMiddleware;
		}

		$this->actions[$actionName]->add(
			$httpMethod,
			StringUtility::isNullOrWhiteSpace($methodName) ? $actionName : $methodName, // @phpstan-ignore-line
			$customMiddleware
		);

		return $this;
	}

	/**
	 * Undocumented function
	 *
	 * @param string $httpMethod
	 * @param Action $action
	 * @param array<string,string> $urlParameters
	 * @return RouteAction
	 */
	private function getActionCore(string $httpMethod, Action $action, array $urlParameters): RouteAction
	{
		$actionValues = $action->get($httpMethod);
		if (is_null($actionValues)) {
			return new RouteAction(
				HttpStatus::methodNotAllowed(),
				$this->className,
				'',
				$urlParameters,
				[]
			);
		}

		return new RouteAction(
			HttpStatus::none(),
			$this->className,
			$actionValues['method'],
			$urlParameters,
			$actionValues['middleware']
		);
	}

	/**
	 * メソッド・リクエストパスから登録されているアクションを取得。
	 *
	 * @param string $httpMethod HttpMethod を参照のこと
	 * @param RequestPath $requestPath リクエストパス
	 * @return RouteAction|null 存在する場合にクラス・メソッドのペア。存在しない場合は null
	 */
	public function getAction(string $httpMethod, RequestPath $requestPath): ?RouteAction
	{
		if (!StringUtility::startsWith($requestPath->full, $this->basePath, false)) {
			return new RouteAction(
				HttpStatus::notFound(),
				$this->className,
				'',
				[],
				[]
			);
		}

		//$actionPath = $requestPaths[count($requestPaths) - 1];
		$actionPath = StringUtility::trimStart(mb_substr($requestPath->full, mb_strlen($this->basePath)), '/');
		$actionPaths = StringUtility::split($actionPath, '/');

		if (!isset($this->actions[$actionPath])) {
			// URLパラメータチェック
			foreach ($this->actions as $key => $action) {
				// 定義内にURLパラメータが無ければ破棄
				if (!StringUtility::contains($key, ':', false)) {
					continue;
				}

				$keyPaths = StringUtility::split($key, '/');
				if (ArrayUtility::getCount($keyPaths) !== ArrayUtility::getCount($actionPaths)) {
					continue;
				}

				$calcPaths = array_filter(array_map(function ($i, $value) use ($actionPaths) {
					$length = StringUtility::getLength($value);
					$targetValue = urldecode($actionPaths[$i]);
					if ($length === 0 || $value[0] !== ':') {
						return ['key' => $value, 'name' => '', 'value' => $targetValue];
					}
					$splitPaths = StringUtility::split($value, '@', 2);
					$requestKey = StringUtility::substring($splitPaths[0], 1);
					$isRegex = 1 < ArrayUtility::getCount($splitPaths);
					if ($isRegex) {
						$pattern = "/$splitPaths[1]/";
						if (Regex::isMatch($targetValue, $pattern)) {
							return ['key' => $value, 'name' => $requestKey, 'value' => $targetValue];
						}
						return null;
					} else {
						return ['key' => $value, 'name' => $requestKey, 'value' => $targetValue];
					}
				}, array_keys($keyPaths), array_values($keyPaths)), function ($i) {
					return !is_null($i);
				});

				// 非URLパラメータ項目は一致するか
				if (ArrayUtility::getCount($calcPaths) !== ArrayUtility::getCount($actionPaths)) {
					continue;
				}
				$success = true;
				for ($i = 0; $i < ArrayUtility::getCount($calcPaths) && $success; $i++) {
					$calcPath = $calcPaths[$i];
					if (StringUtility::isNullOrEmpty($calcPath['name'])) {
						$success = $calcPath['key'] === $actionPaths[$i];
					}
				}
				if (!$success) {
					continue;
				}

				$calcKey = StringUtility::join(array_column($calcPaths, 'key'), '/');
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
				if ($result->status->code() === HttpStatus::none()->code()) {
					return $result;
				}
			}

			return new RouteAction(
				HttpStatus::notFound(),
				$this->className,
				'',
				[],
				[]
			);
		}

		return $this->getActionCore($httpMethod, $this->actions[$actionPath], []);
	}
}
