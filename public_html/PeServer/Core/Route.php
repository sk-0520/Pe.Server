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
	 * @var IActionFilter[]
	 */
	private array $baseFilters;

	/**
	 * Undocumented function
	 *
	 * @param IActionFilter[]|null $options ベースとなるオプション設定。null/0件の場合は IActionFilter::none() が使用される。
	 * @param IActionFilter[] $zero
	 * @param IActionFilter[] $null
	 * @return IActionFilter[]
	 */
	private static function toActions(?array $options, array $zero, array $null): array
	{
		if (is_null($options)) {
			return [];
		}

		if (ArrayUtility::getCount($options)) {
			return $options;
		}

		return $zero;
	}

	/**
	 * ルーティング情報にコントローラを登録
	 *
	 * @param string $path URLとしてのパス。先頭が api 以外の場合に index アクションが自動登録される
	 * @param string $className 使用されるクラス完全名
	 * @param IActionFilter[]|null $filters ベースとなるオプション設定。null/0件の場合は IActionFilter::none() が使用される。
	 */
	public function __construct(string $path, string $className, ?array $filters = null)
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

		$this->baseFilters = self::toActions($filters, [], []);

		$this->className = $className;
		if (mb_substr($this->basePath, 0, 3) != 'api') {
			$this->addAction('', HttpMethod::get(), 'index', $this->baseFilters);
		}
	}

	/**
	 * アクション設定。
	 *
	 * @param string $actionName URLとして使用されるパス, パス先頭が : でURLパラメータとなり、パラメータ名の @ 以降は一致正規表現となる。
	 * @param HttpMethod $httpMethod 使用するHTTPメソッド。
	 * @param string|null $methodName 呼び出されるコントローラメソッド。未指定なら $actionName が使用される。
	 * @param IActionFilter[]|null $filters オプション設定。nullの場合はコンストラクタで渡されたオプションが使用される。0件の場合は IActionFilter::none() が使用される。
	 * @return Route
	 */
	public function addAction(string $actionName, HttpMethod $httpMethod, ?string $methodName = null, ?array $filters = null): Route
	{
		if (!isset($this->actions[$actionName])) {
			$this->actions[$actionName] = new Action();
		}
		$this->actions[$actionName]->add(
			$httpMethod,
			StringUtility::isNullOrWhiteSpace($methodName) ? $actionName : $methodName, // @phpstan-ignore-line
			self::toActions($filters, [], $this->baseFilters)
		);

		return $this;
	}

	/**
	 * Undocumented function
	 *
	 * @param string $httpMethod
	 * @param Action $action
	 * @param array<string,string> $urlParameters
	 * @return array{code:HttpStatus,class:string,method:string,params:array<string,string>,filters:IActionFilter[]}
	 */
	private function getActionCore(string $httpMethod, Action $action, array $urlParameters): array
	{
		$actionValues = $action->get($httpMethod);
		if (is_null($actionValues)) {
			return [
				'code' => HttpStatus::methodNotAllowed(),
				'class' => $this->className,
				'method' => '',
				'params' => $urlParameters,
				'filters' => [],
			];
		}

		return [
			'code' => HttpStatus::none(),
			'class' => $this->className,
			'method' => $actionValues['method'],
			'params' => $urlParameters,
			'filters' => $actionValues['filters'],
		];
	}

	/**
	 * メソッド・リクエストパスから登録されているアクションを取得。
	 *
	 * @param string $httpMethod HttpMethod を参照のこと
	 * @param RequestPath $requestPath リクエストパス
	 * @return array{code:HttpStatus,class:string,method:string,params:array<string,string>,filters:IActionFilter[]}|null 存在する場合にクラス・メソッドのペア。存在しない場合は null
	 */
	public function getAction(string $httpMethod, RequestPath $requestPath): ?array
	{
		if (!StringUtility::startsWith($requestPath->full, $this->basePath, false)) {
			return [
				'code' => HttpStatus::notFound(),
				'class' => $this->className,
				'method' => '',
				'params' => [],
				'filters' => [],
			];
		}

		//$actionPath = $requestPaths[count($requestPaths) - 1];
		$actionPath = ltrim(mb_substr($requestPath->full, mb_strlen($this->basePath)), '/');
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
				if ($result['code']->code() === HttpStatus::none()->code()) {
					return $result;
				}
			}

			return [
				'code' => HttpStatus::notFound(),
				'class' => $this->className,
				'method' => '',
				'params' => [],
				'filters' => [],
			];
		}

		return $this->getActionCore($httpMethod, $this->actions[$actionPath], []);
	}
}
