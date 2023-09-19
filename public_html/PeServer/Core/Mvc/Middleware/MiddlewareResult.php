<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Middleware;

use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Regex;
use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\HttpStatusException;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\Web\UrlUtility;

/**
 * ミドルウェア結果。
 */
abstract class MiddlewareResult
{
	#region define

	protected const RESULT_KIND_NONE = 0;
	protected const RESULT_KIND_STATUS = 1;

	#endregion

	#region variable

	/** 結果なしキャッシュ。 */
	private static ?MiddlewareResult $none;

	#endregion


	/**
	 * 生成。
	 *
	 * @param integer $kind
	 * @phpstan-param self::RESULT_KIND_* $kind
	 */
	protected function __construct(
		/** @readonly */
		private int $kind
	) {
	}

	#region function

	/**
	 * 結果なし。
	 *
	 * 特に何かすることのないミドルウェアはこいつを呼べば問題なし。
	 *
	 * @return MiddlewareResult
	 */
	public static function none(): MiddlewareResult
	{
		return self::$none ??= new class extends MiddlewareResult
		{
			public function __construct()
			{
				parent::__construct(parent::RESULT_KIND_NONE);
			}

			public function apply(): void
			{
				// こいつがここまでくればバグってる
				throw new InvalidOperationException();
			}
		};
	}

	/**
	 * リダイレクト処理生成。
	 *
	 * @param string $path
	 * @param array<non-empty-string,string>|null $query
	 * @param HttpStatus|null $status
	 * @return MiddlewareResult
	 */
	public static function redirect(SpecialStore $specialStore, string $path, ?array $query = null, ?HttpStatus $status = null): MiddlewareResult
	{
		$regex = new Regex();
		if ($regex->isMatch($path, '|(https?:)?//|')) {
			throw new ArgumentException();
		}

		$url = UrlUtility::buildPath($path, $query ?? [], $specialStore);

		return new LocalRedirectMiddlewareResultImpl($status ?? HttpStatus::Found, $url);
	}

	/**
	 * エラー処理生成。
	 *
	 * @param HttpStatus $status
	 * @param string $message
	 * @return MiddlewareResult
	 */
	public static function error(HttpStatus $status, string $message = Text::EMPTY): MiddlewareResult
	{
		return new LocalErrorMiddlewareResultImpl($status, $message);
	}

	/**
	 * 次のミドルウェア処理へ移れるか。
	 *
	 * @return bool 真: 処理可能。
	 */
	final public function canNext(): bool
	{
		return $this->kind === self::RESULT_KIND_NONE;
	}

	/**
	 * ミドルウェア結果適用。
	 */
	abstract public function apply(): void;

	#endregion
}

//phpcs:ignore PSR1.Classes.ClassDeclaration.MultipleClasses
class LocalRedirectMiddlewareResultImpl extends MiddlewareResult
{
	private HttpStatus $status;
	private string $url;

	public function __construct(HttpStatus $status, string $url)
	{
		parent::__construct(parent::RESULT_KIND_STATUS);

		$this->status = $status;
		$this->url = $url;
	}

	public function apply(): void
	{
		header('Location: ' . $this->url, true, $this->status->value);
	}
}

//phpcs:ignore PSR1.Classes.ClassDeclaration.MultipleClasses
class LocalErrorMiddlewareResultImpl extends MiddlewareResult
{
	private HttpStatus $status;
	private string $message;

	public function __construct(HttpStatus $status, string $message)
	{
		parent::__construct(parent::RESULT_KIND_STATUS);

		$this->status = $status;
		$this->message = $message;
	}

	public function apply(): void
	{
		throw new HttpStatusException($this->status, $this->message);
	}
}
