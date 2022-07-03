<?php

declare(strict_types=1);

namespace PeServer\Core;

use \LogicException;
use PeServer\Core\Regex;
use PeServer\Core\Action;
use PeServer\Core\RouteAction;
use PeServer\Core\ArrayUtility;
use PeServer\Core\InitialValue;
use PeServer\Core\StringUtility;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Http\RequestPath;
use PeServer\Core\Mvc\ControllerBase;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Mvc\Middleware\IMiddleware;
use PeServer\Core\Mvc\Middleware\IShutdownMiddleware;

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
	 * @readonly
	 */
	private string $basePath;
	/**
	 * クラス完全名。
	 *
	 * @var string
	 * @readonly
	 * @phpstan-var class-string<ControllerBase>
	 */
	private string $className;
	/**
	 * アクション一覧。
	 *
	 * @var array<string,Action>
	 */
	private array $actions = array();

	/**
	 * Undocumented variable
	 *
	 * @var array<IMiddleware|string>
	 * @phpstan-var array<IMiddleware|class-string<IMiddleware>>
	 */
	private array $baseMiddleware;
	/**
	 * Undocumented variable
	 *
	 * @var array<IShutdownMiddleware|string>
	 * @phpstan-var array<IShutdownMiddleware|class-string<IShutdownMiddleware>>
	 */
	private array $baseShutdownMiddleware;

	protected string $excludeIndexPattern = '/^(api|ajax)/';

	/**
	 * ルーティング情報にコントローラを登録
	 *
	 * @param string $path URLとしてのパス。$this->excludeIndexPattern に一致しない場合に index アクションが自動登録される
	 * @param string $className 使用されるクラス完全名
	 * @phpstan-param class-string<ControllerBase> $className 使用されるクラス完全名
	 * @param array<IMiddleware|string> $middleware ベースとなるミドルウェア。
	 * @phpstan-param array<IMiddleware|class-string<IMiddleware>> $middleware ベースとなるミドルウェア。
	 * @param array<IShutdownMiddleware|string> $shutdownMiddleware ベースとなる終了ミドルウェア。
	 * @phpstan-param array<IShutdownMiddleware|class-string<IShutdownMiddleware>> $shutdownMiddleware ベースとなる終了ミドルウェア。
	 */
	public function __construct(string $path, string $className, array $middleware = [], array $shutdownMiddleware = [])
	{
		if (StringUtility::isNullOrEmpty($path)) {
			$this->basePath = $path;
		} else {
			$trimPath = StringUtility::trim($path);
			if ($trimPath !== StringUtility::trim($trimPath, '/')) {
				throw new LogicException('path start or end -> /');
			}
			//@phpstan-ignore-next-line
			$this->basePath = $trimPath;
		}

		if (ArrayUtility::contains($middleware, self::CLEAR_MIDDLEWARE)) {
			throw new ArgumentException('$middleware');
		}

		$this->baseMiddleware = $middleware;
		$this->baseShutdownMiddleware = $shutdownMiddleware;
		$this->className = $className;

		if (!Regex::isMatch($this->basePath, $this->excludeIndexPattern)) {
			$this->addAction(InitialValue::EMPTY_STRING, HttpMethod::gets(), 'index', $this->baseMiddleware, $this->baseShutdownMiddleware);
		}
	}

	/**
	 * ミドルウェア組み合わせ。
	 *
	 * @param array<IMiddleware|IShutdownMiddleware|string> $baseMiddleware
	 * @phpstan-param array<IMiddleware|IShutdownMiddleware|class-string<IMiddleware|IShutdownMiddleware>|self::CLEAR_MIDDLEWARE> $baseMiddleware
	 * @param array<IMiddleware|IShutdownMiddleware|string>|null $middleware
	 * @phpstan-param array<IMiddleware|IShutdownMiddleware|class-string<IMiddleware|IShutdownMiddleware>|self::CLEAR_MIDDLEWARE>|null $middleware
	 * @return array<IMiddleware|IShutdownMiddleware|string>
	 * @phpstan-return array<IMiddleware|IShutdownMiddleware|class-string<IMiddleware|IShutdownMiddleware>|self::CLEAR_MIDDLEWARE>
	 */
	private static function combineMiddleware(array $baseMiddleware, ?array $middleware = null): array
	{
		$customMiddleware = null;
		if (ArrayUtility::getCount($middleware)) {
			$customMiddleware = [];
			foreach ($middleware as $index => $mw) { // @phpstan-ignore-line ArrayUtility::getCount
				if ($index) {
					if ($mw === self::CLEAR_MIDDLEWARE) {
						throw new ArgumentException();
					}
					$customMiddleware[] = $mw;
				} else {
					if ($mw !== self::CLEAR_MIDDLEWARE) {
						$customMiddleware = array_merge($customMiddleware, $baseMiddleware);
						$customMiddleware[] = $mw;
					}
				}
			}
		} else {
			$customMiddleware = $baseMiddleware;
		}

		return $customMiddleware;
	}

	/**
	 * アクション設定。
	 *
	 * @param string $actionName URLとして使用されるパス, パス先頭が : でURLパラメータとなり、パラメータ名の @ 以降は一致正規表現となる。
	 * @param HttpMethod|HttpMethod[] $httpMethod 使用するHTTPメソッド。
	 * @param string|null $methodName 呼び出されるコントローラメソッド。未指定なら $actionName が使用される。
	 * @param array<IMiddleware|string>|null $middleware 専用ミドルウェア。 第一要素が CLEAR_MIDDLEWARE であれば既存のミドルウェアを破棄する。nullの場合はコンストラクタで渡されたミドルウェアが使用される。
	 * @phpstan-param array<IMiddleware|class-string<IMiddleware>|self::CLEAR_MIDDLEWARE>|null $middleware 専用ミドルウェア。 第一要素が CLEAR_MIDDLEWARE であれば既存のミドルウェアを破棄する。nullの場合はコンストラクタで渡されたミドルウェアが使用される。
	 * @param array<IShutdownMiddleware|string>|null $shutdownMiddleware 専用終了ミドルウェア。 第一要素が CLEAR_MIDDLEWARE であれば既存のミドルウェアを破棄する。nullの場合はコンストラクタで渡されたミドルウェアが使用される。
	 * @phpstan-param array<IShutdownMiddleware|class-string<IShutdownMiddleware>|self::CLEAR_MIDDLEWARE>|null $shutdownMiddleware 専用終了ミドルウェア。 第一要素が CLEAR_MIDDLEWARE であれば既存のミドルウェアを破棄する。nullの場合はコンストラクタで渡されたミドルウェアが使用される。
	 * @return Route
	 */
	public function addAction(string $actionName, HttpMethod|array $httpMethod, ?string $methodName = null, ?array $middleware = null, ?array $shutdownMiddleware = null): Route
	{
		if (!isset($this->actions[$actionName])) {
			$this->actions[$actionName] = new Action();
		}

		/** @phpstan-var array<IMiddleware|class-string<IMiddleware>> */
		$customMiddleware = self::combineMiddleware($this->baseMiddleware, $middleware);
		/** @phpstan-var array<IShutdownMiddleware|class-string<IShutdownMiddleware>> */
		$customShutdownMiddleware = self::combineMiddleware($this->baseShutdownMiddleware, $shutdownMiddleware);

		$this->actions[$actionName]->add(
			$httpMethod,
			StringUtility::isNullOrWhiteSpace($methodName) ? $actionName : $methodName, // @phpstan-ignore-line
			$customMiddleware,
			$customShutdownMiddleware
		);

		return $this;
	}

	/**
	 * アクション取得内部実装。
	 *
	 * @param HttpMethod $httpMethod
	 * @param Action $action
	 * @param array<string,string> $urlParameters
	 * @return RouteAction
	 */
	private function getActionCore(HttpMethod $httpMethod, Action $action, array $urlParameters): RouteAction
	{
		$actionSetting = $action->get($httpMethod);
		if (is_null($actionSetting)) {
			return new RouteAction(
				HttpStatus::methodNotAllowed(),
				$this->className,
				ActionSetting::none(),
				$urlParameters
			);
		}

		return new RouteAction(
			HttpStatus::none(),
			$this->className,
			$actionSetting,
			$urlParameters
		);
	}

	/**
	 * メソッド・リクエストパスから登録されているアクションを取得。
	 *
	 * @param HttpMethod $httpMethod HTTPメソッド。
	 * @param RequestPath $requestPath リクエストパス
	 * @return RouteAction|null 存在する場合にクラス・メソッドのペア。存在しない場合は null
	 */
	public function getAction(HttpMethod $httpMethod, RequestPath $requestPath): ?RouteAction
	{
		if (!StringUtility::startsWith($requestPath->full, $this->basePath, false)) {
			return new RouteAction(
				HttpStatus::notFound(),
				$this->className,
				ActionSetting::none(),
				[]
			);
		}

		//$actionPath = $requestPaths[count($requestPaths) - 1];
		$actionPath = StringUtility::trimStart(StringUtility::substring($requestPath->full, StringUtility::getLength($this->basePath)), '/');
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

				/** @var array<array{key:string,name:string,value:string}> */
				$calcPaths = array_filter(array_map(function ($i, $value) use ($actionPaths) {
					$length = StringUtility::getLength($value);
					$targetValue = urldecode($actionPaths[$i]);
					if ($length === 0 || $value[0] !== ':') {
						return ['key' => $value, 'name' => InitialValue::EMPTY_STRING, 'value' => $targetValue];
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
				}, ArrayUtility::getKeys($keyPaths), ArrayUtility::getValues($keyPaths)), function ($i) {
					return !is_null($i);
				});

				$calcPathLength = ArrayUtility::getCount($calcPaths);
				// 非URLパラメータ項目は一致するか
				if ($calcPathLength !== ArrayUtility::getCount($actionPaths)) {
					continue;
				}
				$success = true;
				for ($i = 0; $i < $calcPathLength && $success; $i++) {
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
				if ($result->status->is(HttpStatus::none())) {
					return $result;
				}
			}

			return new RouteAction(
				HttpStatus::notFound(),
				$this->className,
				ActionSetting::none(),
				[]
			);
		}

		return $this->getActionCore($httpMethod, $this->actions[$actionPath], []);
	}
}
