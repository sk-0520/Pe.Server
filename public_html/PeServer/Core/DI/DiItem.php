<?php

declare(strict_types=1);

namespace PeServer\Core\DI;

use \TypeError;
use PeServer\Core\DisposerBase;
use PeServer\Core\IDisposable;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\Throws\NotSupportedException;
use PeServer\Core\TypeUtility;

/**
 * DI登録値。
 *
 * シングルトンデータは内包する。
 */
class DiItem extends DisposerBase
{
	#region define

	/**
	 * [ライフサイクル] 毎回作る。
	 */
	public const LIFECYCLE_TRANSIENT = 0;
	/**
	 * [ライフサイクル] シングルトン。
	 */
	public const LIFECYCLE_SINGLETON = 1;

	/**
	 * [登録種別] 型。
	 */
	public const TYPE_TYPE = 0;
	/**
	 * [登録種別] 値。
	 */
	public const TYPE_VALUE = 1;
	/**
	 * [登録種別] 生成処理。
	 */
	public const TYPE_FACTORY = 2;

	#endregion

	#region variable

	/**
	 * シングルトンデータを持つか。
	 *
	 * @var bool
	 */
	private bool $hasSingletonValue = false;
	/**
	 * シングルトンデータ。
	 *
	 * @var mixed
	 */
	private mixed $singletonValue = null;

	#endregion

	/**
	 * 生成。
	 *
	 * @param int $lifecycle ライフサイクル。
	 * @phpstan-param self::LIFECYCLE_* $lifecycle
	 * @param int $type 登録種別。
	 * @phpstan-param self::TYPE_* $type
	 * @param string|mixed|callable $data 登録データ。
	 * @phpstan-param class-string|mixed|callable(IDiContainer, DiItem[]):mixed $data
	 * @param bool $nonDisposal IDisposable 対象にするか。
	 */
	public function __construct(
		public int $lifecycle,
		public int $type,
		public mixed $data,
		public bool $nonDisposal = false
	) {
		if ($lifecycle !== self::LIFECYCLE_TRANSIENT && $lifecycle !== self::LIFECYCLE_SINGLETON) { //@phpstan-ignore-line self::LIFECYCLE_*
			throw new ArgumentException('$lifecycle: ' . $lifecycle);
		}

		switch ($type) {
			case self::TYPE_TYPE:
				if (!is_string($data)) {
					throw new TypeError('$data: ' . TypeUtility::getType($data));
				}
				if (Text::isNullOrWhiteSpace($data)) {
					throw new ArgumentException('$data: empty');
				}
				break;

			case self::TYPE_VALUE:
				if ($lifecycle != self::LIFECYCLE_SINGLETON) {
					throw new ArgumentException('$lifecycle: self::LIFECYCLE_SINGLETON');
				}
				$this->hasSingletonValue = true;
				$this->singletonValue = $data;
				break;

			case self::TYPE_FACTORY:
				if (!is_callable($data)) {
					throw new TypeError('$data: ' . TypeUtility::getType($data));
				}
				break;

			default:
				throw new NotSupportedException('$type: ' . $type);
		}
	}

	#region function

	/**
	 * シングルトンデータを保持しているか。
	 *
	 * @return bool
	 */
	public function hasSingletonValue(): bool
	{
		if ($this->lifecycle != self::LIFECYCLE_SINGLETON) {
			return false;
		}

		return $this->hasSingletonValue;
	}


	/**
	 * シングルトンデータを設定。
	 *
	 * DIコンテナ側で処理する想定。
	 *
	 * @param mixed $value
	 * @throws NotSupportedException
	 * @throws InvalidOperationException
	 */
	public function setSingletonValue(mixed $value): void
	{
		if ($this->lifecycle != self::LIFECYCLE_SINGLETON) {
			throw new NotSupportedException();
		}
		if ($this->hasSingletonValue) {
			throw new InvalidOperationException();
		}

		if ($this->type === self::TYPE_TYPE) {
			if (!is_a($value, (string)$this->data)) {
				throw new TypeError(TypeUtility::getType($value) . ' - ' . $this->data);
			}
		}

		$this->singletonValue = $value;
		$this->hasSingletonValue = true;
	}

	/**
	 * シングルトンデータを設定。
	 *
	 * @return mixed
	 * @throws NotSupportedException
	 * @throws InvalidOperationException
	 */
	public function getSingletonValue(): mixed
	{
		if ($this->lifecycle != self::LIFECYCLE_SINGLETON) {
			throw new NotSupportedException();
		}
		if (!$this->hasSingletonValue) {
			throw new InvalidOperationException();
		}

		return $this->singletonValue;
	}

	/**
	 * 型: クラスとして生成。
	 *
	 * @template T
	 * @param string $className
	 * @phpstan-param class-string<T> $className
	 * @param int $lifecycle
	 * @phpstan-param self::LIFECYCLE_* $lifecycle
	 * @return self
	 */
	public static function class(string $className, int $lifecycle = self::LIFECYCLE_TRANSIENT): self
	{
		return new self($lifecycle, self::TYPE_TYPE, $className);
	}

	/**
	 * 値として生成。
	 *
	 * @template T
	 * @param mixed $data
	 * @phpstan-param T $data
	 * @return self
	 */
	public static function value(mixed $data): self
	{
		return new self(self::LIFECYCLE_SINGLETON, self::TYPE_VALUE, $data);
	}

	/**
	 * 生成処理として生成。
	 *
	 * @template T
	 * @param callable $factory
	 * @phpstan-param callable(IDiContainer,DiItem[]):T $factory
	 * @param int $lifecycle
	 * @phpstan-param self::LIFECYCLE_* $lifecycle
	 * @return self
	 */
	public static function factory(callable $factory, int $lifecycle = self::LIFECYCLE_TRANSIENT): self
	{
		return new self($lifecycle, self::TYPE_FACTORY, $factory);
	}

	#endregion

	#region DisposerBase

	protected function disposeImpl(): void
	{
		if ($this->nonDisposal) {
			return;
		}

		/** @var IDisposable|null */
		$disposer = null;

		if ($this->type === DiItem::TYPE_VALUE) {
			if ($this->data instanceof IDisposable) {
				$disposer = $this->data;
			}
		}
		if ($this->lifecycle === DiItem::LIFECYCLE_SINGLETON) {
			if ($this->hasSingletonValue()) {
				$value = $this->getSingletonValue();
				if ($value instanceof IDisposable) {
					$disposer = $value;
				}
			}
		}

		if ($disposer !== null) {
			$disposer->dispose();
		}
	}

	#endregion
}
