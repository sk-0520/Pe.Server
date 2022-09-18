<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

use \Error;
use \Iterator;
use \PDOStatement;
use \Traversable;
use \IteratorIterator;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Database\DatabaseResultBase;
use PeServer\Core\Serialization\Mapper;
use PeServer\Core\Throws\NotSupportedException;
use PeServer\Core\Throws\Throws;

/**
 * 逐次問い合わせ結果。
 *
 * データは保持されない点に注意。
 * `foreach` 一回回したら終了。
 *
 * @template TFieldArray of FieldArrayAlias
 * @implements Iterator<TFieldArray>
 */
class DatabaseSequenceResult extends DatabaseResultBase implements Iterator
{
	#region variable

	/**
	 * @readonly
	 */
	private Iterator $iterator;
	/**
	 * 影響件数。
	 *
	 * 行件数なのか影響件数なのかわけわかんなくなってきた。
	 *
	 * @phpstan-var UnsignedIntegerAlias
	 */
	private int $resultCount = 0;

	#endregion

	/**
	 * 生成。
	 */
	public function __construct(
		array $columns,
		PDOStatement $pdoStatement
	) {
		parent::__construct($columns, 0);

		$this->iterator = new IteratorIterator($pdoStatement);
		$this->resultCount = 0;
	}

	#region

	public function getResultCount(): int
	{
		return $this->resultCount;
	}

	#endregion

	#region Iterator

	public function rewind(): void
	{
		Throws::wrap(Error::class, NotSupportedException::class, fn () => $this->iterator->rewind());
	}

	/**
	 * @return int
	 * @phpstan-return UnsignedIntegerAlias
	 */
	public function key(): mixed
	{
		return $this->iterator->key();
	}

	/**
	 * @phpstan-return TFieldArray
	 */
	public function current(): mixed
	{
		return $this->iterator->current();
	}

	public function next(): void
	{
		$this->iterator->next();
		$this->resultCount += 1;
	}

	public function valid(): bool
	{
		return $this->iterator->valid();
	}

	#endregion

}
