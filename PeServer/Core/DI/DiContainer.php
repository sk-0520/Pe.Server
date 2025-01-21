<?php

declare(strict_types=1);

namespace PeServer\Core\DI;

use ReflectionClass;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;
use ReflectionType;
use ReflectionUnionType;
use TypeError;
use PeServer\Core\Collection\Arr;
use PeServer\Core\DI\DiItem;
use PeServer\Core\DI\Inject;
use PeServer\Core\DI\IScopedDiContainer;
use PeServer\Core\DI\ScopedDiContainer;
use PeServer\Core\DisposerBase;
use PeServer\Core\IDisposable;
use PeServer\Core\ReflectionUtility;
use PeServer\Core\Text;
use PeServer\Core\Throws\DiContainerArgumentException;
use PeServer\Core\Throws\DiContainerNotFoundException;
use PeServer\Core\Throws\DiContainerUndefinedTypeException;
use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\TypeUtility;

/**
 * DIコンテナ実装。
 */
class DiContainer extends DisposerBase implements IDiContainer
{
	#region variable

	/**
	 * IDとの紐づけ。
	 *
	 * @var array<class-string|non-empty-string,DiItem>
	 */
	protected array $mapping = [];

	#endregion

	#region function

	/**
	 * 登録アイテムを具象クラス名も考慮して取得する。
	 *
	 * @param class-string|non-empty-string $idOrClassName 登録アイテムID
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

	/**
	 *
	 * @param null|ReflectionType $parameterType
	 * @param mixed $value
	 * @return bool
	 * @phpstan-pure
	 */
	protected function canSetValue(?ReflectionType $parameterType, mixed $value): bool
	{
		if ($value === null) {
			return false;
		}
		if (!is_object($value)) {
			return false;
		}

		foreach (ReflectionUtility::getTypes($parameterType) as $currentType) {
			$typeName = $currentType->getName();
			if (is_a($value, $typeName)) {
				return true;
			}
		}

		return false;
	}

	protected function getItemFromPropertyType(?ReflectionType $parameterType, bool $mappingKeyOnly): ?DiItem
	{
		foreach (ReflectionUtility::getTypes($parameterType) as $currentType) {
			/** @var class-string */
			$typeName = $currentType->getName();
			$item = $this->getMappingItem($typeName, $mappingKeyOnly);
			if ($item !== null) {
				return $item;
			}
		}

		return null;
	}

	/**
	 * 生成オブジェクトに対するパラメータ一覧を生成する。
	 *
	 * @param ReflectionFunctionAbstract $reflectionMethod
	 * @param array<array-key,mixed> $arguments `IDiContainer::new` 参照。
	 * @param int $level 現在階層(0: 最初)
	 * @param bool $mappingKeyOnly 真の場合は登録アイテムIDのみに限定。偽の場合、登録されている具象クラス名を考慮する。
	 * @param DiItem[] $callStack
	 * @return array<mixed>
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 */
	protected function generateParameterValues(ReflectionFunctionAbstract $reflectionMethod, array $arguments, int $level, bool $mappingKeyOnly, array $callStack): array
	{
		$result = [];

		$canDynamicArgument = !empty($arguments);
		$dynamicArgumentKeys = $canDynamicArgument ? array_filter($arguments, fn ($k) => is_int($k) && $k < 0, ARRAY_FILTER_USE_KEY) : [];
		if ($canDynamicArgument && !empty($dynamicArgumentKeys)) {
			$dynamicArgumentKeys = array_keys($dynamicArgumentKeys);
			rsort($dynamicArgumentKeys, SORT_NUMERIC);
		}

		foreach ($reflectionMethod->getParameters() as $parameter) {
			$parameterType = $parameter->getType();

			/** @var DiItem|null */
			$item = null;

			// 引数指定
			if (!empty($arguments)) {
				$isHit = false;
				$argument = null;
				if (isset($arguments[$parameter->getPosition()])) {
					// 引数位置指定
					$isHit = true;
					$argument = $arguments[$parameter->getPosition()];
				} else {
					$parameterName = '$' . $parameter->name;
					if (isset($arguments[$parameterName])) {
						// 引数名
						$isHit = true;
						$argument = $arguments[$parameterName];
					} else {
						// 型名
						$types = ReflectionUtility::getTypes($parameterType);
						foreach ($types as $type) {
							if (isset($arguments[$type->getName()])) {
								$isHit = true;
								$argument = $arguments[$type->getName()];
								unset($arguments[$type->getName()]);
								break;
							}
						}
					}
				}

				// -1 以下の割り当て可能パラメータを適用
				if (!$isHit && $canDynamicArgument) {
					foreach ($dynamicArgumentKeys as $i => $key) {
						if ($this->canSetValue($parameterType, $arguments[$key])) {
							$argument = $arguments[$key];
							$isHit = true;
							unset($dynamicArgumentKeys[$i]);
							$canDynamicArgument = !empty($dynamicArgumentKeys);
							break;
						}
					}
				}

				if ($isHit) {
					$result[$parameter->getPosition()] = $argument;
					continue;
				}
			}

			// 属性指定
			$attributes = $parameter->getAttributes(Inject::class);
			if (!Arr::isNullOrEmpty($attributes)) {
				$attribute = $attributes[0]->newInstance();
				if (!Text::isNullOrWhiteSpace($attribute->id)) {
					$id = $attribute->id;
					$item = $this->getMappingItem($id, $mappingKeyOnly);
				}
			}

			if ($item === null) {
				$item = $this->getItemFromPropertyType($parameterType, $mappingKeyOnly);
			}

			// 未登録
			if ($item === null) {
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

			$parameterValue = $this->create($item, [], $level + 1, $mappingKeyOnly, [...$callStack, $item]);
			$result[$parameter->getPosition()] = $parameterValue;
		}

		return $result;
	}

	/**
	 * クラス名からオブジェクトの生成。
	 *
	 * @param class-string $className
	 * @param array<array-key,mixed> $arguments `IDiContainer::new` 参照。
	 * @param int $level 現在階層(0: 最初)
	 * @param bool $mappingKeyOnly 真の場合は登録アイテムIDのみに限定。偽の場合、登録されている具象クラス名を考慮する。
	 * @param DiItem[] $callStack
	 * @return mixed
	 */
	protected function createFromClassName(string $className, array $arguments, int $level, bool $mappingKeyOnly, array $callStack): mixed
	{
		$classReflection = new ReflectionClass($className);
		$constructor = $classReflection->getConstructor();
		if ($constructor === null) {
			return new $className();
		}

		$parameters = $this->generateParameterValues($constructor, $arguments, $level, $mappingKeyOnly, $callStack);

		$result = new $className(...$parameters);
		$this->setMembers($result, $level, $mappingKeyOnly, $callStack);

		return $result;
	}

	/**
	 * メンバ インジェクション
	 *
	 * @param object $target
	 * @param int $level 現在階層(0: 最初)
	 * @param bool $mappingKeyOnly 真の場合は登録アイテムIDのみに限定。偽の場合、登録されている具象クラス名を考慮する。
	 * @param DiItem[] $callStack
	 */
	protected function setMembers(object $target, int $level, bool $mappingKeyOnly, array $callStack): void
	{
		$reflectionClass = new ReflectionClass($target);

		// $properties = $reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE);

		// $parent = $reflectionClass;
		// while ($parent = $parent->getParentClass()) {
		// 	$parentProperties = $parent->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE);
		// 	$properties = array_merge($properties, $parentProperties); //いっつもわからんくなる。何が正しいのか
		// }
		$properties = ReflectionUtility::getAllProperties($reflectionClass);

		foreach ($properties as $property) {
			// コンストラクタからプロパティになりあがっている場合はそっとしておく
			if ($property->isPromoted()) {
				continue;
			}

			$attributes = $property->getAttributes(Inject::class);

			if (!Arr::isNullOrEmpty($attributes)) {
				/** @var DiItem|null */
				$item = null;

				/** @var Inject */
				$attribute = $attributes[0]->newInstance();
				if (!Text::isNullOrWhiteSpace($attribute->id)) {
					$id = $attribute->id;
					$item = $this->getMappingItem($id, $mappingKeyOnly);
				}

				if ($item === null) {
					$propertyType = $property->getType();
					$item = $this->getItemFromPropertyType($propertyType, $mappingKeyOnly);
				}

				// 設定できない場合は何もしない
				if ($item !== null) {
					$callStack[] = $item;
					$propertyValue = $this->create($item, [], $level + 1, $mappingKeyOnly, $callStack);
					$property->setAccessible(true);
					$property->setValue($target, $propertyValue);
				}
			}
		}
	}

	/**
	 * 生成処理。
	 *
	 * @param DiItem $item
	 * @param array<array-key,mixed> $arguments `IDiContainer::new` 参照。
	 * @param int $level 現在階層(0: 最初)
	 * @param bool $mappingKeyOnly 真の場合は登録アイテムIDのみに限定。偽の場合、登録されている具象クラス名を考慮する。
	 * @param DiItem[] $callStack
	 * @return mixed
	 */
	protected function create(DiItem $item, array $arguments, int $level, bool $mappingKeyOnly, array $callStack): mixed
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
			/** @var class-string */
			$className = (string)$item->data;
			$result = $this->createFromClassName($className, $arguments, $level, $mappingKeyOnly, $callStack);
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

	#endregion

	#region IDiContainer

	public function has(string $id): bool //@phpstan-ignore-line [TYPE_INTERFACE]
	{
		return isset($this->mapping[$id]);
	}

	public function get(string $id): object //@phpstan-ignore-line [TYPE_INTERFACE]
	{
		$this->throwIfDisposed();

		if (!$this->has($id)) {
			throw new DiContainerNotFoundException($id);
		}

		$item = $this->mapping[$id];

		return $this->create($item, [], 0, false, [$item]);
	}

	public function new(string $idOrClassName, array $arguments = []): object
	{
		$this->throwIfDisposed();

		if ($this->has($idOrClassName) && empty($arguments)) {
			return $this->get($idOrClassName);
		}

		$item = $this->getMappingItem($idOrClassName, false);
		if ($item !== null) {
			if (!empty($arguments)) {
				if ($item->type === DiItem::TYPE_VALUE) {
					throw new DiContainerArgumentException($idOrClassName . ': DiItem::TYPE_VALUE');
				}
				if ($item->lifecycle === DiItem::LIFECYCLE_SINGLETON) {
					throw new DiContainerArgumentException($idOrClassName . ': DiItem::LIFECYCLE_SINGLETON');
				}
			}

			return $this->create($item, $arguments, 0, false, [$item]);
		}

		/** @var class-string $idOrClassName */
		return $this->createFromClassName($idOrClassName, $arguments, 0, false, [DiItem::class($idOrClassName)]);
	}

	public function call(callable $callback, array $arguments = []): mixed
	{
		/** @var ReflectionFunctionAbstract|null */
		$reflectionFunc = null;

		if (is_string($callback)) {
			$methodArray = Text::split($callback, '::');
			$methodCount = Arr::getCount($methodArray);
			if ($methodCount === 0 || 2 < $methodCount) {
				throw new TypeError('$callback: ' . $callback);
			}
			if ($methodCount === 1) {
				$reflectionFunc = new ReflectionFunction($callback);
			} else {
				$reflectionClass = new ReflectionClass($methodArray[0]); //@phpstan-ignore-line クラスと信じるしかないやん
				$reflectionFunc = $reflectionClass->getMethod($methodArray[1]);
			}
		} elseif (is_array($callback)) {
			if (Arr::getCount($callback) !== 2) {
				throw new TypeError('$callback: ' . Text::dump($callback));
			}
			$reflectionClass = new ReflectionClass($callback[0]);
			$reflectionFunc = $reflectionClass->getMethod($callback[1]);
		} elseif (is_callable($callback)) {
			$reflectionFunc = new ReflectionFunction($callback); //@phpstan-ignore-line
		}

		if ($reflectionFunc === null) { //@phpstan-ignore-line
			throw new TypeError('$callback: ' . Text::dump($callback));
		}

		$item = new DiItem(DiItem::LIFECYCLE_TRANSIENT, DiItem::TYPE_TYPE, $reflectionFunc->name, true);
		$parameters = $this->generateParameterValues($reflectionFunc, $arguments, 0, false, [$item]);

		return call_user_func_array($callback, $parameters);
	}

	public function clone(): IScopedDiContainer
	{
		return new ScopedDiContainer($this);
	}

	#endregion

	#region DisposerBase

	protected function disposeImpl(): void
	{
		foreach ($this->mapping as $item) {
			$item->dispose();
		}

		$this->mapping = [];
	}

	#endregion
}
