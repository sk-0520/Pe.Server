<?php

declare(strict_types=1);

namespace PeServer\Core\DI;

use Generator;
use PeServer\Core\ArrayUtility;
use PeServer\Core\DI\DiItem;
use PeServer\Core\DI\Inject;
use PeServer\Core\DisposerBase;
use PeServer\Core\IDisposable;
use PeServer\Core\ReflectionUtility;
use PeServer\Core\Text;
use PeServer\Core\Throws\DiContainerNotFoundException;
use PeServer\Core\Throws\DiContainerUndefinedTypeException;
use PeServer\Core\Throws\NotImplementedException;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;
use ReflectionType;
use ReflectionUnionType;

class DiContainer extends DisposerBase implements IDiContainer
{
	/**
	 * IDとの紐づけ。
	 *
	 * @var array<string,DiItem>
	 * @phpstan-var array<class-string|non-empty-string,DiItem>
	 */
	protected array $mapping = [];

	/**
	 * 登録アイテムを具象クラス名も考慮して取得する。
	 *
	 * @param string $idOrClassName 登録アイテムID
	 * @phpstan-param class-string|non-empty-string $idOrClassName
	 * @param bool $mappingKeyOnly 真の場合は登録アイテムIDのみに限定。偽の場合、登録されている具象クラス名を考慮する。
	 * @return DiItem|null
	 */
	protected function getMappingItem(string $idOrClassName, bool $mappingKeyOnly): ?DiItem
	{
		if ($this->has($idOrClassName)) {
			return $this->mapping[$idOrClassName];
		}
		if ($mappingKeyOnly) {
			return null;
		}

		foreach ($this->mapping as $item) {
			if ($item->type !== DiItem::TYPE_TYPE) {
				continue;
			}

			$itemTypeName = (string)$item->data;
			if ($itemTypeName === $idOrClassName) {
				return $item;
			}
		}

		return null;
	}

	protected function getItemFromPropertyType(?ReflectionType $parameterType, bool $mappingKeyOnly): ?DiItem
	{
		if ($parameterType instanceof ReflectionNamedType) {
			/** @phpstan-var class-string */
			$typeName = $parameterType->getName();
			return $this->getMappingItem($typeName, $mappingKeyOnly);
		}

		if ($parameterType instanceof ReflectionUnionType) {
			// UNION型は先頭から適用を探す
			foreach ($parameterType->getTypes() as $currentType) {
				$typeName = $currentType->getName();
				if (!Text::isNullOrEmpty($typeName)) {
					$item = $this->getMappingItem($typeName, $mappingKeyOnly);
					if (!is_null($item)) {
						return $item;
					}
				}
			}
		}

		return null;
	}

	/**
	 * 生成オブジェクトに対するパラメータ一覧を生成する。
	 *
	 * @param ReflectionMethod $reflectionMethod
	 * @param int $level 現在階層(0: 最初)
	 * @param bool $mappingKeyOnly 真の場合は登録アイテムIDのみに限定。偽の場合、登録されている具象クラス名を考慮する。
	 * @param DiItem[] $callStack
	 * @return array<mixed>
	 */
	protected function generateParameterValues(ReflectionMethod $reflectionMethod, int $level, bool $mappingKeyOnly, array $callStack): array
	{
		$result = [];

		foreach ($reflectionMethod->getParameters() as $parameter) {
			$parameterType = $parameter->getType();

			/** @var DiItem|null */
			$item = null;

			// 属性指定を優先する
			$attributes = $parameter->getAttributes(Inject::class);
			if (!ArrayUtility::isNullOrEmpty($attributes)) {
				/** @var Inject */
				$attribute = $attributes[0]->newInstance();
				if (!Text::isNullOrWhiteSpace($attribute->id)) {
					/** @phpstan-var class-string */
					$id = $attribute->id;
					$item = $this->getMappingItem($id, $mappingKeyOnly);
				}
			}

			if (is_null($item)) {
				$item = $this->getItemFromPropertyType($parameterType, $mappingKeyOnly);
			}

			// 未登録
			if (is_null($item)) {
				if ($parameter->isDefaultValueAvailable()) {
					$result[$parameter->getPosition()] = $parameter->getDefaultValue();
					continue;
				}
				if ($parameter->allowsNull()) {
					$result[$parameter->getPosition()] = null;
					continue;
				}

				throw new DiContainerUndefinedTypeException($parameter->name);
			}

			//@phpstan-ignore-next-line null時未実装
			$parameterValue = $this->create($item, $level + 1, $mappingKeyOnly, [...$callStack, $item]);
			$result[$parameter->getPosition()] = $parameterValue;
		}

		return $result;
	}

	/**
	 * クラス名からオブジェクトの生成。
	 *
	 * @param string $className
	 * @phpstan-param class-string $className
	 * @param int $level 現在階層(0: 最初)
	 * @param bool $mappingKeyOnly 真の場合は登録アイテムIDのみに限定。偽の場合、登録されている具象クラス名を考慮する。
	 * @param DiItem[] $callStack
	 * @return mixed
	 */
	protected function createFromClassName(string $className, int $level, bool $mappingKeyOnly, array $callStack): mixed
	{
		$classReflection = new ReflectionClass($className);
		$constructor = $classReflection->getConstructor();
		if (is_null($constructor)) {
			return new $className();
		}

		$parameters = $this->generateParameterValues($constructor, $level, $mappingKeyOnly, $callStack);

		return new $className(...$parameters);
	}

	/**
	 * メンバインジェクション
	 *
	 * @param object $target
	 * @param int $level 現在階層(0: 最初)
	 * @param bool $mappingKeyOnly 真の場合は登録アイテムIDのみに限定。偽の場合、登録されている具象クラス名を考慮する。
	 * @param DiItem[] $callStack
	 */
	protected function setMembers(object $target, int $level, bool $mappingKeyOnly, array $callStack): void
	{
		$reflectionClass = new ReflectionClass($target);
		$properties = $reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE);

		foreach ($properties as $property) {
			// コンストラクタからプロパティになりあがっている場合はそっとしておく
			if ($property->isPromoted()) {
				continue;
			}

			$attributes = $property->getAttributes(Inject::class);

			if (!ArrayUtility::isNullOrEmpty($attributes)) {
				/** @var DiItem|null */
				$item = null;

				/** @var Inject */
				$attribute = $attributes[0]->newInstance();
				if (!Text::isNullOrWhiteSpace($attribute->id)) {
					/** @phpstan-var class-string */
					$id = $attribute->id;
					$item = $this->getMappingItem($id, $mappingKeyOnly);
				}

				if (is_null($item)) {
					$propertyType = $property->getType();
					$item = $this->getItemFromPropertyType($propertyType, $mappingKeyOnly);
				}

				// 設定できない場合は何もしない
				if (!is_null($item)) {
					$callStack[] = $item;
					$propertyValue = $this->create($item, $level + 1, $mappingKeyOnly, $callStack);
					$name = $property->name;
					$target->$name = $propertyValue;
				}
			}
		}
	}

	/**
	 * 生成処理。
	 *
	 * @param DiItem $item
	 * @param int $level 現在階層(0: 最初)
	 * @param bool $mappingKeyOnly 真の場合は登録アイテムIDのみに限定。偽の場合、登録されている具象クラス名を考慮する。
	 * @param DiItem[] $callStack
	 * @return mixed
	 */
	protected function create(DiItem $item, int $level, bool $mappingKeyOnly, array $callStack): mixed
	{
		// 値は何も考えなくていい
		if ($item->type === DiItem::TYPE_VALUE) {
			return $item->data;
		}

		// 既にシングルトンデータを持っているならそのまま返却
		if ($item->lifecycle === DiItem::LIFECYCLE_SINGLETON && $item->hasSingletonValue()) {
			return $item->getSingletonValue();
		}

		$result = null;

		if ($item->type === DiItem::TYPE_TYPE) {
			/** @phpstan-var class-string */
			$className = (string)$item->data;
			$result = $this->createFromClassName($className, $level, $mappingKeyOnly, $callStack);
		} else {
			assert($item->type === DiItem::TYPE_FACTORY); //@phpstan-ignore-line
			$result = call_user_func($item->data, $this, $callStack);
		}

		if (is_object($result)) {
			$this->setMembers($result, $level, $mappingKeyOnly, $callStack);
		}

		if ($item->lifecycle === DiItem::LIFECYCLE_SINGLETON) {
			$item->setSingletonValue($result);
		}

		return $result;
	}

	/**
	 * クラス生成。
	 *
	 * @param string $idOrClassName
	 * @phpstan-param class-string|non-empty-string $idOrClassName
	 * @return mixed
	 */
	public function new(string $idOrClassName): mixed
	{
		if ($this->has($idOrClassName)) {
			return $this->get($idOrClassName);
		}

		$item = $this->getMappingItem($idOrClassName, false);
		if (!is_null($item)) {
			return $this->create($item, 0, false, [$item]);
		}

		/** @phpstan-var class-string $idOrClassName */
		return $this->createFromClassName($idOrClassName, 0, false, []);
	}

	//[IDiContainer]

	public function has(string $id): bool
	{
		return isset($this->mapping[$id]);
	}

	public function get(string $id): mixed
	{
		if (!$this->has($id)) {
			throw new DiContainerNotFoundException($id);
		}

		$item = $this->mapping[$id];

		return $this->create($item, 0, false, [$item]);
	}

	//[DisposerBase]

	protected function disposeImpl(): void
	{
		foreach ($this->mapping as $item) {
			/** @var IDisposable|null */
			$disposer = null;
			if ($item->type === DiItem::TYPE_VALUE) {
				if ($item->data instanceof IDisposable) {
					$disposer = $item->data;
				}
			}
			if ($item->lifecycle === DiItem::LIFECYCLE_SINGLETON) {
				if ($item->hasSingletonValue()) {
					$value = $item->getSingletonValue();
					if ($value instanceof IDisposable) {
						$disposer = $value;
					}
				}
			}

			if (!is_null($disposer)) {
				$disposer->dispose();
			}
		}
	}
}
