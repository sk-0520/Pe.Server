<?php

declare(strict_types=1);

namespace PeServer\Core;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;
use ReflectionUnionType;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\TypeException;

/**
 * 型とかそんなやつになんかするやつ。
 */
abstract class ReflectionUtility
{
	#region function

	/**
	 * クラスオブジェクトの生成。
	 *
	 * @template TObject of object
	 * @param string $input 生成クラス名。
	 * @phpstan-param class-string<TObject> $input
	 * @param string|object $baseClass 基底クラス。オブジェクトを渡した場合は生成クラスの型チェックに使用される。
	 * @phpstan-param class-string|object $baseClass
	 * @return object 生成インスタンス。
	 * @phpstan-return TObject
	 * @throws TypeException 型おかしい。
	 */
	public static function create(string $input, string|object $baseClass, mixed ...$parameters): object
	{
		if (!class_exists($input)) {
			throw new TypeException();
		}

		$input = new $input(...$parameters);

		if (is_string($baseClass)) {
			if (!is_a($input, $baseClass, false)) {
				throw new TypeException();
			}
		} else {
			$baseClassName = get_class($baseClass);
			if (!is_a($input, $baseClassName, false)) {
				throw new TypeException();
			}
		}

		/** @phpstan-var TObject */
		return $input;
	}

	/**
	 * `method_exists` ラッパー。
	 *
	 * @param string|object $input
	 * @phpstan-param class-string|object $input
	 * @param string $method
	 * @phpstan-param non-empty-string $method
	 */
	public static function existsMethod(object|string $input, string $method): bool
	{
		if (is_string($input)) {
			if (Text::isNullOrWhiteSpace($input)) { //@phpstan-ignore-line [DOCTYPE]
				throw new ArgumentException('$input');
			}
		}

		if (Text::isNullOrWhiteSpace($method)) { //@phpstan-ignore-line [DOCTYPE]
			throw new ArgumentException('$method');
		}

		return method_exists($input, $method);
	}

	/**
	 * 対象と継承元の全てのプロパティを取得。
	 *
	 * @template T of object
	 * @param ReflectionClass $current
	 * @phpstan-param ReflectionClass<T> $current
	 * @param int $filter
	 * @return ReflectionProperty[]
	 */
	public static function getAllProperties(ReflectionClass $current, int $filter = ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE): array
	{
		$properties = $current->getProperties($filter);

		$parent = $current;
		while ($parent = $parent->getParentClass()) {
			$parentProperties = $parent->getProperties($filter);
			$properties = array_merge($properties, $parentProperties); //いっつもわからんくなる。何が正しいのか
		}

		return $properties;
	}

	/**
	 * 型指定から型一覧を取得。
	 *
	 * @param ReflectionType|null $parameterType
	 * @return ReflectionNamedType[]
	 */
	public static function getTypes(?ReflectionType $parameterType): array
	{
		if ($parameterType instanceof ReflectionNamedType) {
			return [$parameterType];
		}

		if ($parameterType instanceof ReflectionUnionType) {
			$result = [];

			foreach ($parameterType->getTypes() as $type) {
				if ($type instanceof ReflectionNamedType) {
					$result[] = $type;
				}
			}

			return $result;
		}

		return [];
	}

	#endregion
}
