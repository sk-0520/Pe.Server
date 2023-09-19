<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

use Error;
use Iterator;
use PDOStatement;
use Traversable;
use IteratorIterator;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Database\DatabaseResultBase;
use PeServer\Core\Serialization\IMapper;
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

	#region DatabaseResultBase

	/**
	 * 結果をマッピングしたイテレータの返却。
	 *
	 * @template TObject of object
	 * @param string $className
	 * @phpstan-param class-string<TObject> $className
	 * @param IMapper|null $mapper
	 * @return Iterator
	 * @phpstan-return Iterator<TObject>
	 */
	public function mapping(string $className, IMapper $mapper = null): Iterator
	{
		return new LocalSequenceIterator($this, $className, $mapper ?? new Mapper());
	}

	#endregion

	#region DatabaseResultBase

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

/**
 * @template TFieldArray of FieldArrayAlias
 * @template TObject of object
 * @implements Iterator<TObject>
 */
//phpcs:ignore PSR1.Classes.ClassDeclaration.MultipleClasses
class LocalSequenceIterator extends DatabaseResultBase implements Iterator
{
	/**
	 * 生成。
	 *
	 * @param DatabaseSequenceResult $sequence
	 * @phpstan-param DatabaseSequenceResult<TFieldArray> $sequence
	 * @param string $className
	 * @phpstan-param class-string<TObject> $className
	 * @param IMapper $mapper
	 */
	public function __construct(
		private DatabaseSequenceResult $sequence,
		private string $className,
		private IMapper $mapper
	) {
	}

	#region Iterator

	public function rewind(): void
	{
		$this->sequence->rewind();
	}

	/**
	 * @return int
	 * @phpstan-return UnsignedIntegerAlias
	 */
	public function key(): mixed
	{
		return $this->sequence->key();
	}

	/**
	 * @phpstan-return TObject
	 */
	public function current(): mixed
	{
		$fields = $this->sequence->current();
		/** @var TObject */
		return $this->mappingImpl($fields, $this->className, $this->mapper);
	}

	public function next(): void
	{
		$this->sequence->next();
	}

	public function valid(): bool
	{
		return $this->sequence->valid();
	}

	#endregion
}
