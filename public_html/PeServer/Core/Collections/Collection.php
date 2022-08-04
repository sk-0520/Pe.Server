<?php

declare(strict_types=1);

namespace PeServer\Core\Collections;

use \AppendIterator;
use \CallbackFilterIterator;
use \Countable;
use \EmptyIterator;
use \Iterator;
use \IteratorAggregate;
use \LimitIterator;
use \Traversable;
use PeServer\Core\ArrayUtility;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\TypeUtility;

/**
 * イテレータを使用したコレクション処理(LINQしたいのだ)。
 *
 * 実行速度ではなく、開発効率を目標としている。
 *
 * @template TKey of array-key
 * @template TValue
 * @phpstan-type PredicateAlias callable(TValue,TKey):(bool)
 */
class Collection implements IteratorAggregate // @phpstan-ignore-line
{
	/** @phpstan-var Iterator<TKey,TValue> */
	private Iterator $iterator;

	/**
	 * 生成。
	 *
	 * @param Iterator $iterator
	 */
	private function __construct(Iterator $iterator)
	{
		$this->iterator = $iterator;
	}

	/**
	 * イテレータから生成。
	 *
	 * @template TCreateKey of array-key
	 * @template TCreateValue
	 * @param Iterator $iterator
	 * @phpstan-param Iterator<TCreateKey,TCreateValue> $iterator
	 * @return Collection
	 * @phpstan-return Collection<TCreateKey,TCreateValue>
	 */
	private static function create(Iterator $iterator): Collection
	{
		return new Collection($iterator);
	}

	/**
	 * ジェネレータから生成。
	 *
	 * @template TWrapKey of array-key
	 * @template TWrapValue
	 * @param callable $generatorFactory
	 * @phpstan-param callable():(\Generator<TWrapKey,TWrapValue>) $generatorFactory
	 * @return Collection
	 * @phpstan-return Collection<TWrapKey,TWrapValue>
	 */
	private static function wrap(callable $generatorFactory): Collection
	{
		return self::create(CollectionUtility::toIterator($generatorFactory));
	}

	/**
	 * @phpstan-return Traversable<TKey,TValue>
	 */
	public function getIterator(): Traversable
	{
		return $this->iterator;
	}

	// 開始 ----------------------------------------------------

	/**
	 * 配列からコレクション生成。
	 *
	 * @template TFromKey of array-key
	 * @template TFromValue
	 * @param Traversable|array<mixed>|callable $sequence
	 * @phpstan-param Traversable<TFromKey,TFromValue>|array<TFromKey,TFromValue>|callable():(\Generator<TFromKey,TFromValue>) $sequence
	 * @return Collection
	 * @phpstan-return Collection<TFromKey,TFromValue>
	 */
	public static function from(Traversable|array|callable $sequence): Collection
	{
		return self::create(CollectionUtility::toIterator($sequence));
	}

	/**
	 * 指定した範囲内の整数からコレクション生成。
	 *
	 * @param int $start 開始。
	 * @param int $count 件数。
	 * @phpstan-param UnsignedIntegerAlias $count
	 * @return Collection
	 * @phpstan-return Collection<UnsignedIntegerAlias,int>
	 */
	public static function range(int $start, int $count): Collection
	{
		$iterator = new RangeIterator($start, $count);
		/** @phpstan-var Collection<UnsignedIntegerAlias,int> */
		return self::create($iterator);
	}

	/**
	 * 繰り返されるコレクション生成。
	 *
	 * @template TRepeatValue
	 * @param mixed $value 値。
	 * @phpstan-param TRepeatValue $value
	 * @param int $count 件数。
	 * @phpstan-param UnsignedIntegerAlias $count
	 * @return Collection
	 * @phpstan-return Collection<UnsignedIntegerAlias,TRepeatValue>
	 */
	public static function repeat(mixed $value, int $count): Collection
	{
		$iterator = new RepeatIterator($value, $count);
		/** @phpstan-var Collection<UnsignedIntegerAlias,TRepeatValue> */
		return self::create($iterator);
	}

	/**
	 * 空のコレクション。
	 *
	 * @return Collection
	 * @phpstan-return Collection<TKey,TValue>
	 */
	public static function empty(): Collection
	{
		return self::create(new EmptyIterator());
	}

	// 実体化 ----------------------------------------------------

	/**
	 * 配列実体化。
	 *
	 * @return array<mixed>
	 * @phpstan-return array<array-key,TValue>
	 */
	public function toArray(): array
	{
		return CollectionUtility::toArray($this->iterator, false);
	}

	/**
	 * リスト実体化。
	 *
	 * `Enumerable.ToList<TSource>` 的な。
	 *
	 * @param string $type
	 * @phpstan-param class-string|TypeUtility::TYPE_* $type
	 * @return Vector
	 * @phpstan-return Vector<TValue>
	 */
	public function toList(string $type = TypeUtility::TYPE_UNKNOWN): Vector
	{
		$array = self::toArray();
		if (ArrayUtility::isNullOrEmpty($array)) {
			return Vector::empty($type);
		}
		/** @phpstan-var non-empty-array<TValue> $array */

		return Vector::create($array, true);
	}

	/**
	 * 辞書実体化。
	 *
	 * @template TResult
	 * @param callable $keyFactory 第1引数:値, 第1引数:キー(文字列変換済み), 戻り値:キー(文字列)。
	 * @phpstan-param callable(TValue,TKey):string $keyFactory
	 * @param callable $valueFactory 第1引数:値, 第1引数:キー(文字列変換済み), 戻り値:値。
	 * @phpstan-param callable(TValue,TKey):TResult $valueFactory
	 * @param string $type
	 * @phpstan-param class-string|TypeUtility::TYPE_* $type
	 * @return Dictionary
	 * @phpstan-return Dictionary<TResult>
	 */
	public function toDictionary(callable $keyFactory, callable $valueFactory, string $type = TypeUtility::TYPE_UNKNOWN): Dictionary
	{
		/** @phpstan-var array<string,TResult> */
		$buffer = [];

		foreach ($this->iterator as $key => $value) {
			$convertedKey = $key;
			if (!is_string($key)) {
				$convertedKey = TypeUtility::toString($key);
			}

			$k = call_user_func($keyFactory, $value, $convertedKey);
			$v = call_user_func($valueFactory, $value, $convertedKey);

			if (isset($buffer[$k])) {
				throw new ArgumentException($k);
			}

			$buffer[$k] = $v;
		}

		if (ArrayUtility::isNullOrEmpty($buffer)) {
			Dictionary::empty($type);
		}
		/** @phpstan-var non-empty-array<string,TResult> $buffer */

		return Dictionary::create($buffer);
	}

	// 処理 ----------------------------------------------------

	/**
	 * [遅延] フィルタリング
	 *
	 * @param callable $callback 値, キー: 条件を満たすか
	 * @phpstan-param PredicateAlias $callback
	 * @return Collection
	 * @phpstan-return Collection<TKey,TValue>
	 */
	public function where(callable $callback): self
	{
		$iterator = new CallbackFilterIterator($this->iterator, $callback);
		return self::create($iterator);
	}

	/**
	 * [遅延] 射影。
	 *
	 * @template TResult
	 * @param callable $callback
	 * @phpstan-param callable(TValue,TKey):(TResult) $callback
	 * @return Collection
	 * @phpstan-return Collection<TKey,TResult>
	 */
	public function select(callable $callback): self
	{
		$selectIterator = new SelectIterator($this->iterator, $callback);
		return self::create($selectIterator);
	}

	/**
	 * [遅延] 射影-平坦化。
	 *
	 * @template TResult
	 * @param callable $callback
	 * @phpstan-param callable(TValue,TKey):(TResult) $callback
	 * @return Collection
	 * @phpstan-return Collection<TKey,TResult>
	 */
	public function selectMany(callable $callback): self
	{
		//@phpstan-ignore-next-line
		$selectIterator = new SelectManyIterator($this->iterator, $callback);
		return new Collection($selectIterator);
	}


	/**
	 * [遅延] 末尾に連結。
	 *
	 * @param Traversable|array<mixed>|callable $sequence
	 * @phpstan-param Traversable<TKey,TValue>|array<TKey,TValue>|callable():(\Generator) $sequence
	 * @return self
	 * @phpstan-return Collection<TKey,TValue>
	 */
	public function concat(Traversable|array|callable $sequence): self
	{
		$sequenceIterator = CollectionUtility::toIterator($sequence);

		/** @phpstan-var AppendIterator<TKey,TValue,Iterator<TKey,TValue>> */
		$appendIterator = new AppendIterator();
		$appendIterator->append($this->iterator);
		$appendIterator->append($sequenceIterator);

		return self::create($appendIterator);
	}

	/**
	 * [遅延] 要素を先頭追加。
	 *
	 * @param mixed $value
	 * @phpstan-param TValue $value
	 * @return self
	 * @phpstan-return Collection<TKey,TValue>
	 */
	public function prepend(mixed $value): self
	{
		$valueIterator = CollectionUtility::toIterator([$value]);

		/** @phpstan-var AppendIterator<TKey,TValue,Iterator<TKey,TValue>> */
		$appendIterator = new AppendIterator();
		$appendIterator->append($valueIterator);
		$appendIterator->append($this->iterator);

		return self::create($appendIterator);
	}

	/**
	 * [遅延] 要素を末尾追加。
	 *
	 * @param mixed $value
	 * @phpstan-param TValue $value
	 * @return self
	 * @phpstan-return Collection<TKey,TValue>
	 */
	public function append(mixed $value): self
	{
		$valueIterator = CollectionUtility::toIterator([$value]);

		/** @phpstan-var AppendIterator<TKey,TValue,Iterator<TKey,TValue>> */
		$appendIterator = new AppendIterator();
		$appendIterator->append($this->iterator);
		$appendIterator->append($valueIterator);

		return self::create($appendIterator);
	}

	/**
	 * [即時] 要素が含まれているか。
	 *
	 * @param callable|null $callback 非nullの場合条件指定。
	 * @phpstan-param PredicateAlias|null $callback
	 * @return boolean
	 */
	public function any(?callable $callback = null): bool
	{
		if (is_null($callback)) {
			$this->iterator->rewind();
			return $this->iterator->valid();
		}

		foreach ($this->iterator as $key => $value) {
			if ($callback($value, $key)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * [即時] 全ての要素が条件を満たすか。
	 *
	 * @param callable $callback
	 * @phpstan-param PredicateAlias $callback
	 * @return boolean
	 */
	public function all(callable $callback): bool
	{
		foreach ($this->iterator as $key => $value) {
			if (!$callback($value, $key)) {
				return false;
			}
		}

		return true;
	}

	/**
	 * [即時] 件数を取得。
	 *
	 * @param callable|null $callback 非nullの場合条件指定。
	 * @phpstan-param PredicateAlias|null $callback
	 * @return int
	 */
	public function count(callable $callback = null): int
	{
		if (is_null($callback)) {
			if ($this->iterator instanceof Countable) {
				return $this->iterator->count();
			}

			return iterator_count($this->iterator);
		}

		$count = 0;
		$this->iterator->rewind();
		while ($this->iterator->valid()) {
			if ($callback($this->iterator->current(), $this->iterator->key())) {
				$count += 1;
			}
			$this->iterator->next();
		}

		return $count;
	}

	/**
	 * [即時] 先頭要素を取得。
	 *
	 * @param callable|null $callback 非nullの場合条件指定。
	 * @phpstan-param PredicateAlias|null $callback
	 * @return mixed
	 * @phpstan-return TValue
	 * @throws InvalidOperationException 要素なし。
	 */
	public function first(?callable $callback = null): mixed
	{
		if (is_null($callback)) {
			foreach ($this->iterator as $key => $value) {
				return $value;
			}
		} else {
			foreach ($this->iterator as $key => $value) {
				if ($callback($value, $key)) {
					return $value;
				}
			}
		}

		throw new InvalidOperationException();
	}

	/**
	 * [即時] 先頭要素を取得、存在しない場合は指定値を返す。
	 *
	 * @param mixed $notFound 存在しない場合の戻り値。
	 * @phpstan-param TValue $notFound
	 * @param callable|null $callback 非nullの場合条件指定。
	 * @phpstan-param PredicateAlias|null $callback
	 * @return mixed
	 * @phpstan-return TValue
	 */
	public function firstOr(mixed $notFound, ?callable $callback = null): mixed
	{
		if (is_null($callback)) {
			foreach ($this->iterator as $key => $value) {
				return $value;
			}
		} else {
			foreach ($this->iterator as $key => $value) {
				if ($callback($value, $key)) {
					return $value;
				}
			}
		}

		return $notFound;
	}

	/**
	 * [即時] 終端要素を取得。
	 *
	 * @param callable|null $callback 非nullの場合条件指定。
	 * @phpstan-param PredicateAlias|null $callback
	 * @return mixed
	 * @phpstan-return TValue
	 * @throws InvalidOperationException 要素なし。
	 */
	public function last(?callable $callback = null): mixed
	{
		$isFound = false;
		/** @phpstan-var TValue */
		$current = null;

		if (is_null($callback)) {
			foreach ($this->iterator as $key => $value) {
				$isFound = true;
				$current = $value;
			}
		} else {
			foreach ($this->iterator as $key => $value) {
				if ($callback($value, $key)) {
					$isFound = true;
					$current = $value;
				}
			}
		}

		if ($isFound) {
			return $current;
		}

		throw new InvalidOperationException();
	}

	/**
	 * [即時] 終端要素を取得、存在しない場合は指定値を返す。
	 *
	 * @param mixed $notFound 存在しない場合の戻り値。
	 * @phpstan-param TValue $notFound
	 * @param callable|null $callback 非nullの場合条件指定。
	 * @phpstan-param PredicateAlias|null $callback
	 * @return mixed
	 * @phpstan-return TValue
	 * @throws InvalidOperationException 要素なし。
	 */
	public function lastOr(mixed $notFound, ?callable $callback = null): mixed
	{
		$isFound = false;
		/** @phpstan-var TValue */
		$current = null;

		if (is_null($callback)) {
			foreach ($this->iterator as $key => $value) {
				$isFound = true;
				$current = $value;
			}
		} else {
			foreach ($this->iterator as $key => $value) {
				if ($callback($value, $key)) {
					$isFound = true;
					$current = $value;
				}
			}
		}

		if ($isFound) {
			return $current;
		}

		return $notFound;
	}

	/**
	 * [即時] 単独の要素取得。
	 *
	 * @param callable|null $callback 非nullの場合条件指定。
	 * @phpstan-param PredicateAlias|null $callback
	 * @return mixed
	 * @phpstan-return TValue
	 * @throws InvalidOperationException 要素なし/複数あり。
	 */
	public function single(?callable $callback = null): mixed
	{
		$isFound = false;
		/** @phpstan-var TValue */
		$current = null;

		if (is_null($callback)) {
			foreach ($this->iterator as $key => $value) {
				if ($isFound) {
					throw new InvalidOperationException();
				}
				$isFound = true;
				$current = $value;
			}
		} else {
			foreach ($this->iterator as $key => $value) {
				if ($callback($value, $key)) {
					if ($isFound) {
						throw new InvalidOperationException();
					}
					$isFound = true;
					$current = $value;
				}
			}
		}

		if ($isFound) {
			return $current;
		}

		throw new InvalidOperationException();
	}

	/**
	 * [即時] 単独の要素取得、存在しない場合は指定値を返す。
	 *
	 * @param mixed $notFound 存在しない場合の戻り値。
	 * @phpstan-param TValue $notFound
	 * @param callable|null $callback 非nullの場合条件指定。
	 * @phpstan-param PredicateAlias|null $callback
	 * @return mixed
	 * @phpstan-return TValue
	 * @throws InvalidOperationException 複数あり。
	 */
	public function singleOr(mixed $notFound, ?callable $callback = null): mixed
	{
		$isFound = false;
		/** @phpstan-var TValue */
		$current = null;

		if (is_null($callback)) {
			foreach ($this->iterator as $key => $value) {
				if ($isFound) {
					throw new InvalidOperationException();
				}
				$isFound = true;
				$current = $value;
			}
		} else {
			foreach ($this->iterator as $key => $value) {
				if ($callback($value, $key)) {
					if ($isFound) {
						throw new InvalidOperationException();
					}
					$isFound = true;
					$current = $value;
				}
			}
		}

		if ($isFound) {
			return $current;
		}

		return $notFound;
	}

	/**
	 * [遅延] 先頭から指定数をバイパス。
	 *
	 * @param int $skipCount
	 * @phpstan-param UnsignedIntegerAlias $skipCount
	 * @return self
	 * @phpstan-return Collection<TKey,TValue>
	 */
	public function skip(int $skipCount): self
	{
		$limitIterator = new LimitIterator($this->iterator, $skipCount);
		return self::create($limitIterator);
	}

	/**
	 * [即時] 先頭から条件を満たす限りバイパス。
	 *
	 * @param callable $callback
	 * @phpstan-param PredicateAlias $callback
	 * @return self
	 * @phpstan-return Collection<TKey,TValue>
	 */
	public function skipWhile(callable $callback): self
	{
		return self::wrap(function () use ($callback) {
			$skipCount = 0;
			foreach ($this->iterator as $key => $value) {
				if (!$callback($value, $key)) {
					foreach ($this->skip($skipCount) as $key => $value) {
						yield $key => $value;
					}
				}
				$skipCount += 1;
			}

			return self::empty();
		});
	}

	/**
	 * [即時] 先頭から指定された件数を返却。
	 *
	 * @param int $takeCount
	 * @phpstan-param UnsignedIntegerAlias $takeCount
	 * @return self
	 * @phpstan-return Collection<TKey,TValue>
	 */
	public function take(int $takeCount): self
	{
		return self::create(new TakeIterator($this->iterator, $takeCount));
	}

	/**
	 * [即時] 先頭から条件を満たすデータを返却。
	 *
	 * @param callable $callback
	 * @phpstan-param PredicateAlias $callback
	 * @return self
	 * @phpstan-return Collection<TKey,TValue>
	 */
	public function takeWhile(callable $callback): self
	{
		return self::create(new TakeWhileIterator($this->iterator, $callback));
	}

	/**
	 * [遅延] 反転。
	 *
	 * @return Collection
	 * @phpstan-return Collection<TKey,TValue>
	 */
	public function reverse(): Collection
	{
		return self::wrap(function () {
			$cache = [];
			foreach ($this->iterator as $key => $value) {
				$cache[] = [$key, $value];
			}
			$count = count($cache);
			for ($i = $count - 1; $i >= 0; $i--) {
				yield $cache[$i][0] => $cache[$i][1];
			}
		});
	}

	/**
	 * [即時] 集計。
	 *
	 * @param callable $callback
	 * @phpstan-param callable(TValue $result,TValue,TKey):(TValue) $callback
	 * @param mixed $initial
	 * @phpstan-param TValue $initial
	 * @phpstan-return TValue
	 */
	public function aggregate(callable $callback, mixed $initial = 0): mixed
	{
		$result = $initial;

		foreach ($this->iterator as $key => $value) {
			$result = $callback($result, $value, $key);
		}

		return $result;
	}

	/**
	 * [遅延] zip
	 *
	 * @template TSequenceKey of array-key
	 * @template TSequenceValue
	 * @template TResult
	 * @param Traversable|array|callable $sequence
	 * @phpstan-param Traversable<TSequenceKey,TSequenceValue>|array<TSequenceKey,TSequenceValue>|callable():(\Generator) $sequence
	 * @param callable $callback
	 * @phpstan-param callable(array{0:TValue,1:TSequenceValue},TKey):(TResult) $callback
	 * @return Collection
	 * @phpstan-return Collection<array-key,TResult>
	 */
	public function zip(Traversable|array|callable $sequence, callable $callback): Collection
	{
		$sequenceIterator = CollectionUtility::toIterator($sequence);
		$iterator = new ZipIterator($this->iterator, $sequenceIterator, $callback);
		return self::create($iterator);
	}
}
