<?php

declare(strict_types=1);

namespace PeServer\Core\Web;

use \Stringable;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Collections\Collection;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\InvalidOperationException;

/**
 * URL のパス構成要素。
 */
readonly class UrlPath implements Stringable
{
	#region variable

	/**
	 * 構成要素
	 *
	 * `null` の場合はほんとになんもない(ホストの後の `/` もない)
	 * 配列要素数が 0 の場合は `/` のみ
	 *
	 * @var string[]|null
	 */
	private array|null $pathElements;


	#endregion

	public function __construct(string $path)
	{
		if (Text::isNullOrWhiteSpace($path)) {
			$this->pathElements = null;
		} else {
			$elements = Collection::from(Text::split($path, '/'))
				->select(fn ($a) => Text::trim($a, '/'))
				->where(fn ($a) => !Text::isNullOrWhiteSpace($a))
				->toArray();

			foreach ($elements as $element) {
				if (!self::isValidElement($element)) {
					throw new ArgumentException($path);
				}
			}

			$this->pathElements = $elements;
		}
	}

	#region function

	/**
	 * パスの各要素から生成。
	 *
	 * @param string[] $elements
	 * @return self
	 */
	public static function from(array $elements): self
	{
		return new self(Text::join('/', $elements));
	}

	public static function isValidElement(string $element): bool
	{
		$invalids = ['/', '?', '#'];

		foreach ($invalids as $invalid) {
			if (Text::contains($element, $invalid, false)) {
				return false;
			}
		}

		return true;
	}

	/**
	 * ルートの `/` すら持たない空のパスか。
	 *
	 * @return bool
	 * @phpstan-assert-if-true null $this->pathElements
	 * @phpstan-assert-if-false string[] $this->pathElements
	 */
	public function isEmpty(): bool
	{
		return $this->pathElements === null;
	}

	/**
	 * パスの各要素を取得。
	 *
	 * @return string[]
	 */
	public function getElements(): array
	{
		if ($this->isEmpty()) {
			throw new InvalidOperationException('empty');
		}

		return $this->pathElements;
	}

	/**
	 * 終端パスを追加。
	 *
	 * @param string $element
	 * @return self 終端パスの追加された `UrlPath`
	 */
	public function add(string $element): self
	{
		if (Text::isNullOrWhiteSpace($element)) {
			return $this;
		}

		if ($this->isEmpty()) {
			return new self($element);
		} else {
			return self::from([...$this->pathElements, $element]);
		}
	}

	public function toString(bool $addLastSeparator): string
	{
		if ($this->isEmpty()) {
			return Text::EMPTY;
		}

		if (!Arr::getCount($this->pathElements)) {
			return '/';
		}

		return '/' . Text::join('/', $this->pathElements) . ($addLastSeparator ? '/' : Text::EMPTY);
	}

	#endregion

	#region Stringable

	public function __toString(): string
	{
		return $this->toString(false);
	}

	#endregion
}
