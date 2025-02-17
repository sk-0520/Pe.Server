<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Routing;

use PeServer\Core\Collection\Arr;
use PeServer\Core\Code;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Http\RequestPath;
use PeServer\Core\Mvc\Action\Action;
use PeServer\Core\Mvc\Action\ActionSetting;
use PeServer\Core\Mvc\Controller\ControllerBase;
use PeServer\Core\Mvc\Middleware\IMiddleware;
use PeServer\Core\Mvc\Middleware\IShutdownMiddleware;
use PeServer\Core\Regex;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;

/**
 * ルーティング情報。
 */
class RouteInformation
{
	#region define

	/** ミドルウェア指定時に以前をリセットする。 */
	public const CLEAR_MIDDLEWARE = '*';

	#endregion

	#region variable

	/**
	 * ベースパス。
	 */
	private readonly string $basePath;
	/**
	 * クラス完全名。
	 *
	 * @var class-string<ControllerBase>
	 */
	private readonly string $className;
	/**
	 * アクション一覧。
	 *
	 * @var array<string,Action>
	 */
	private array $actions = [];

	/**
	 * ミドルウェア一覧。
	 *
	 * @var array<IMiddleware|class-string<IMiddleware>>
	 */
	private array $baseMiddleware;
	/**
	 * 終了ミドルウェア一覧。
	 *
	 * @var array<IShutdownMiddleware|class-string<IShutdownMiddleware>>
	 */
	private array $baseShutdownMiddleware;

	private Regex $regex;

	#endregion

	/**
	 * ルーティング情報にコントローラを登録
	 *
	 * @param string $path URLとしてのパス。$this->excludeIndexPattern に一致しない場合に index アクションが自動登録される
	 * @param class-string<ControllerBase> $className 使用されるクラス完全名
	 * @param array<IMiddleware|class-string<IMiddleware>> $middleware ベースとなるミドルウェア。
	 * @param array<IShutdownMiddleware|class-string<IShutdownMiddleware>> $shutdownMiddleware ベースとなる終了ミドルウェア。
	 */
	public function __construct(string $path, string $className, array $middleware = [], array $shutdownMiddleware = [])
	{
		$this->regex = new Regex();

		if (Text::isNullOrEmpty($path)) {
			$this->basePath = $path;
		} else {
			$trimPath = Text::trim($path);
			if ($trimPath !== Text::trim($trimPath, '/')) {
				throw new ArgumentException('path start or end -> /');
			}
			$this->basePath = $trimPath;
		}

		if (Arr::containsValue($middleware, self::CLEAR_MIDDLEWARE)) {
			throw new ArgumentException('$middleware');
		}

		$this->baseMiddleware = $middleware;
		$this->baseShutdownMiddleware = $shutdownMiddleware;
		$this->className = $className;

		if (!$this->regex->isMatch($this->basePath, $this->getExcludeIndexPattern())) {
			$this->addAction(Text::EMPTY, HttpMethod::gets(), 'index', $this->baseMiddleware, $this->baseShutdownMiddleware);
		}
	}

	#region function

	/**
	 * コントローラに対してインデックスを付与しないパターン。
	 *
	 * * APIとかにインデックスは不要となる
	 * * このパターンに該当しない場合、無名のアクションとして `index` メソッドが自動登録される。
	 *
	 * @return string
	 * @phpstan-return literal-string
	 */
	protected function getExcludeIndexPattern(): string
	{
		return '/\A(api|ajax)/';
	}

	/**
	 * ミドルウェア組み合わせ。
	 *
	 * @template TMiddleware of IMiddleware|IShutdownMiddleware
	 *
	 * @param array<IMiddleware|IShutdownMiddleware|class-string> $baseMiddleware
	 * @phpstan-param array<TMiddleware|class-string<TMiddleware>> $baseMiddleware
	 * @param array<IMiddleware|IShutdownMiddleware|class-string>|null $middleware
	 * @phpstan-param array<TMiddleware|class-string<TMiddleware>|self::CLEAR_MIDDLEWARE>|null $middleware
	 * @return array<IMiddleware|IShutdownMiddleware|class-string>
	 * @phpstan-return array<TMiddleware|class-string<TMiddleware>>
	 */
	private static function combineMiddleware(array $baseMiddleware, ?array $middleware = null): array
	{
		$customMiddleware = null;
		if (Arr::getCount($middleware)) {
			$customMiddleware = [];
			foreach ($middleware as $index => $mw) { // @phpstan-ignore-line Arr::getCount
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
	 * @param string $methodName 呼び出されるコントローラメソッド。
	 * @param array<IMiddleware|string>|null $middleware 専用ミドルウェア。 第一要素が CLEAR_MIDDLEWARE であれば既存のミドルウェアを破棄する。nullの場合はコンストラクタで渡されたミドルウェアが使用される。
	 * @phpstan-param array<IMiddleware|class-string<IMiddleware>|self::CLEAR_MIDDLEWARE>|null $middleware
	 * @param array<IShutdownMiddleware|string>|null $shutdownMiddleware 専用終了ミドルウェア。 第一要素が CLEAR_MIDDLEWARE であれば既存のミドルウェアを破棄する。nullの場合はコンストラクタで渡されたミドルウェアが使用される。
	 * @phpstan-param array<IShutdownMiddleware|class-string<IShutdownMiddleware>|self::CLEAR_MIDDLEWARE>|null $shutdownMiddleware
	 * @return RouteInformation
	 */
	public function addAction(string $actionName, HttpMethod|array $httpMethod, string $methodName, ?array $middleware = null, ?array $shutdownMiddleware = null): RouteInformation
	{
		if (Text::isNullOrWhiteSpace($methodName)) {
			throw new ArgumentException('$methodName');
		}

		if (!isset($this->actions[$actionName])) {
			$this->actions[$actionName] = new Action();
		}

		$customMiddleware = self::combineMiddleware($this->baseMiddleware, $middleware);
		$customShutdownMiddleware = self::combineMiddleware($this->baseShutdownMiddleware, $shutdownMiddleware);

		$this->actions[$actionName]->add(
			$httpMethod,
			$methodName,
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
	 * @param array<non-empty-string,string> $urlParameters
	 * @return RouteAction
	 */
	private function getActionCore(HttpMethod $httpMethod, Action $action, array $urlParameters): RouteAction
	{
		$actionSetting = $action->get($httpMethod);
		if ($actionSetting === null) {
			return new RouteAction(
				HttpStatus::MethodNotAllowed,
				$this->className,
				ActionSetting::none(),
				$urlParameters
			);
		}

		return new RouteAction(
			HttpStatus::None,
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
		if (!Text::startsWith($requestPath->full, $this->basePath, false)) {
			return new RouteAction(
				HttpStatus::NotFound,
				$this->className,
				ActionSetting::none(),
				[]
			);
		}

		//$actionPath = $requestPaths[count($requestPaths) - 1];
		$actionPath = Text::trimStart(Text::substring($requestPath->full, Text::getLength($this->basePath)), '/');
		$actionPaths = Text::split($actionPath, '/');

		if (!isset($this->actions[$actionPath])) {
			// URLパラメータチェック
			foreach ($this->actions as $key => $action) {
				// 定義内にURLパラメータが無ければ破棄
				if (!Text::contains($key, ':', false)) {
					continue;
				}

				$keyPaths = Text::split($key, '/');
				if (Arr::getCount($keyPaths) !== Arr::getCount($actionPaths)) {
					continue;
				}

				/** @var array<array{key:string,name:string,value:string}> */
				$calcPaths = array_filter(array_map(function ($i, $value) use ($actionPaths) {
					$length = Text::getLength($value);
					$targetValue = urldecode($actionPaths[$i]);
					if ($length === 0 || $value[0] !== ':') {
						return ['key' => $value, 'name' => Text::EMPTY, 'value' => $targetValue];
					}
					$splitPaths = Text::split($value, '@', 2);
					$requestKey = Text::substring($splitPaths[0], 1);
					$isRegex = 1 < Arr::getCount($splitPaths);
					if ($isRegex) {
						$pattern = Code::toLiteralString("/$splitPaths[1]/");
						if ($this->regex->isMatch($targetValue, $pattern)) {
							return ['key' => $value, 'name' => $requestKey, 'value' => $targetValue];
						}
						return null;
					} else {
						return ['key' => $value, 'name' => $requestKey, 'value' => $targetValue];
					}
				}, Arr::getKeys($keyPaths), Arr::getValues($keyPaths)), function ($i) {
					return $i !== null;
				});

				$calcPathLength = Arr::getCount($calcPaths);
				// 非URLパラメータ項目は一致するか
				if ($calcPathLength !== Arr::getCount($actionPaths)) {
					continue;
				}
				$success = true;
				for ($i = 0; $i < $calcPathLength && $success; $i++) {
					$calcPath = $calcPaths[$i];
					if (Text::isNullOrEmpty($calcPath['name'])) {
						$success = $calcPath['key'] === $actionPaths[$i];
					}
				}
				if (!$success) {
					continue;
				}

				$calcKey = Text::join('/', array_column($calcPaths, 'key'));
				if ($key !== $calcKey) {
					continue;
				}

				$calcParameters = array_filter($calcPaths, function ($i) {
					return !Text::isNullOrEmpty($i['name']);
				});

				$flatParameters = [];
				foreach ($calcParameters as $calcParameter) {
					$flatParameters[$calcParameter['name']] = $calcParameter['value'];
				}

				$result = $this->getActionCore($httpMethod, $action, $flatParameters);
				if ($result->status === HttpStatus::None) {
					return $result;
				}
			}

			return new RouteAction(
				HttpStatus::NotFound,
				$this->className,
				ActionSetting::none(),
				[]
			);
		}

		return $this->getActionCore($httpMethod, $this->actions[$actionPath], []);
	}

	#endregion
}
