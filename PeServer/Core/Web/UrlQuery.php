<?php

declare(strict_types=1);

namespace PeServer\Core\Web;

use Stringable;
use PeServer\Core\Binary;
use PeServer\Core\Collection\Arr;
use PeServer\Core\Collection\Collections;
use PeServer\Core\Encoding;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\ArgumentNullException;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\TypeUtility;

/**
 * URL のクエリ構成要素。
 *
 * 並びなぁ、結構つらめ
 */
readonly class UrlQuery implements Stringable
{
	#region variable

	/**
	 * 構成要素
	 *
	 * `null` の場合はほんとになんもない(`?` がない)
	 * 配列要素数が 0 の場合は `?` のみ
	 *
	 * @var array<string,(string|null)[]>|null
	 */
	private array|null $items;


	#endregion

	/**
	 * 生成
	 *
	 * @param string|array<string,(string|null)[]>|null $query クエリ。配列を渡す場合は `from` を使用すること(ある程度補完される)。
	 * @param UrlEncoding|null $urlEncoding
	 */
	public function __construct(string|array|null $query, ?UrlEncoding $urlEncoding = null)
	{
		if ($query === null) {
			$this->items = null;
		} elseif (is_array($query)) {
			foreach ($query as $key => $value) {
				if (!is_array($value)) { //@phpstan-ignore-line [DOCTYPE]
					throw new ArgumentException("\$query: [$key] value not array");
				}
				foreach ($value as $i => $v) {
					if (!($v === null || is_string($v))) { //@phpstan-ignore-line [DOCTYPE]
						$s = TypeUtility::getType($v);
						throw new ArgumentException("\$query: [$key] value $i:[$s] not string|null");
					}
				}
			}
			$this->items = $query;
		} else {
			$urlEncoding ??= UrlEncoding::createDefault();

			/** @var array<string,(string|null)[]> */
			$work = [];

			$queries = Text::split($query, '&');
			foreach ($queries as $rawQuery) {
				$rawKeyValues = Text::split($rawQuery, '=', 2);
				$rawKey = $rawKeyValues[0];
				if (Text::isNullOrEmpty($rawKey)) {
					continue;
				}
				$rawValue = isset($rawKeyValues[1]) ? $rawKeyValues[1] : null;

				$key = $urlEncoding->decode($rawKey);
				if (!isset($work[$key])) {
					$work[$key] = [];
				}
				if ($rawValue === null) {
					$work[$key][] = null;
				} else {
					$value = $urlEncoding->decode($rawValue);
					$work[$key][] = $value;
				}
			}

			$this->items = $work;
		}
	}

	#region function

	/**
	 * 配列から生成。
	 *
	 * 少しくらいは融通をつける。
	 * NOTE: bool はどう変換(t/true/TRUE/on)すればいいか分からんので面倒見ない
	 *
	 * @param array<string,(string|int|null)[]|string|int|null> $query
	 * @return self
	 */
	public static function from(array $query, ?UrlEncoding $urlEncoding = null): self
	{
		/** @var array<string,(string|null)[]> */
		$workQuery = [];

		foreach ($query as $key => $value) {
			/** @var (string|null)[] */
			$workValues = [];

			if ($value !== null) {
				if (is_string($value)) {
					$workValues = [$value];
				} elseif (is_int($value)) {
					$workValues = [(string)$value];
				} elseif (is_array($value)) {
					foreach ($value as $i => $v) {
						if ($v === null || is_string($v)) {
							$workValues[] = $v;
						} elseif (is_int($v)) { //@phpstan-ignore-line [DOCTYPE]
							$workValues[] = (string)$v;
						} else {
							$s = TypeUtility::getType($v);
							throw new ArgumentException("\$query: [$key] value $i:[$s] not string|int|null");
						}
					}
				} else {
					throw new ArgumentException("\$query: [$key] value not string|int|array|null");
				}
			}

			$workQuery[$key] = $workValues;
		}

		return new self($workQuery, $urlEncoding);
	}

	/**
	 * クエリ部分が完全に存在しないか。
	 *
	 * @return bool
	 * @phpstan-assert-if-true null $this->items
	 * @phpstan-assert-if-false array<string,(string|null)[]> $this->items
	 * @phpstan-pure
	 */
	public function isEmpty(): bool
	{
		return $this->items === null;
	}

	/**
	 * 現在のクエリ状態を取得。
	 *
	 * @return array<string,(string|null)[]>
	 */
	public function getQuery(): array
	{
		if ($this->isEmpty()) {
			throw new InvalidOperationException('empty');
		}

		return $this->items;
	}


	/**
	 * Undocumented function
	 *
	 * @param UrlEncoding $urlEncoding
	 * @return string
	 */
	public function toString(?UrlEncoding $urlEncoding = null): string
	{
		if ($this->isEmpty()) {
			return Text::EMPTY;
		}

		if (!Arr::getCount($this->items)) {
			return '?';
		}

		$urlEncoding ??= UrlEncoding::createDefault();

		$kvItems = [];
		foreach ($this->items as $key => $values) {
			$encKey = $urlEncoding->encode($key);
			foreach ($values as $value) {
				if ($value === null) {
					$kvItems[] = $encKey;
				} else {
					$encValue = $urlEncoding->encode($value);
					$kvItems[] = "$encKey=$encValue";
				}
			}
		}

		return '?' . Text::join('&', $kvItems);
	}

	#endregion

	#region Stringable

	public function __toString(): string
	{
		return $this->toString();
	}

	#endregion
}
