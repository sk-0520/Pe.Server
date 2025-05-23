<?php

declare(strict_types=1);

namespace PeServer\Core\Serialization;

use ArrayAccess;
use PeServer\Core\Collections\Arr;
use PeServer\Core\ReflectionUtility;
use PeServer\Core\Serialization\Converter\TypeConverterBase;
use PeServer\Core\Serialization\IMapper;
use PeServer\Core\Text;
use PeServer\Core\Throws\KeyNotFoundException;
use PeServer\Core\Throws\MapperKeyNotFoundException;
use PeServer\Core\Throws\MapperTypeException;
use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\TypeUtility;
use ReflectionClass;
use ReflectionEnum;
use ReflectionNamedType;
use TypeError;

/**
 * マッピング通常処理。
 */
class Mapper implements IMapper
{
	#region variable

	/**
	 * 人畜無害なマッピング設定。
	 */
	private readonly Mapping $defaultMapping;

	#endregion

	public function __construct()
	{
		$this->defaultMapping = new Mapping(Text::EMPTY, Mapping::FLAG_NONE);
	}

	#region IMapper

	public function mapping(array|ArrayAccess $source, object $destination): void
	{
		$destReflection = new ReflectionClass($destination);
		$properties = ReflectionUtility::getAllProperties($destReflection);
		$ignoreKeys = [];

		foreach ($properties as $property) {
			$property->setAccessible(true);
			$attrs = $property->getAttributes(Mapping::class);

			$mapping = $this->defaultMapping;

			if (!empty($attrs)) {
				$attr = $attrs[0];

				/** @var Mapping */
				$mapping = $attr->newInstance();

				// 無視する
				if ($mapping->flags === Mapping::FLAG_IGNORE) {
					continue;
				}
			}

			$keyName = $property->name;
			if (!Text::isNullOrWhiteSpace($mapping->name)) {
				$keyName = $mapping->name;
			}

			// 指定したキーが見つからない
			if (!isset($source[$keyName])) {
				if (($mapping->flags & Mapping::FLAG_EXCEPTION_NOT_FOUND_KEY) === Mapping::FLAG_EXCEPTION_NOT_FOUND_KEY) {
					throw new MapperKeyNotFoundException('$keyName: ' . $keyName);
				}
				// 例外指定がないので現在キーは無視する
				continue;
			}

			if (isset($ignoreKeys[$keyName])) {
				continue;
			}

			$sourceValue = $source[$keyName];
			$ignoreKeys[$keyName] = true; //対象キーは不要なので破棄
			$sourceType = TypeUtility::getType($sourceValue);
			$propertyTypes = ReflectionUtility::getTypes($property->getType());
			foreach ($propertyTypes as $propertyType) {
				$nestTypeName = $propertyType->getName();
				$isArrayObject = $nestTypeName === TypeUtility::TYPE_ARRAY && class_exists($mapping->arrayValueClassName);

				if (!$isArrayObject) {
					if ($sourceType === $nestTypeName) {
						$property->setValue($destination, $sourceValue);
						continue 2; // loop: $properties
					}

					if (enum_exists($nestTypeName)) {
						$destEnum = new ReflectionEnum($nestTypeName);
						$destCases = $destEnum->getCases();
						foreach ($destCases as $destCase) {
							$enumValue = $destCase->getValue();
							if (is_string($sourceValue) && Text::toUpper($enumValue->name) === Text::toUpper($sourceValue)) {
								$property->setValue($destination, $enumValue);
								continue 3; // loop: $properties
							}

							if (property_exists($enumValue, "value") && $enumValue->value === $sourceValue) {
								$property->setValue($destination, $enumValue);
								continue 3; // loop: $properties
							}
						}

						$property->setValue($destination, $destCases[0]->getValue());
						continue 2; // loop: $properties
					}
				}

				if ($sourceType === TypeUtility::TYPE_ARRAY) {
					$nestDestination = null;
					if ($property->isInitialized($destination)) {
						$nestDestination = $property->getValue($destination);
					}

					if ($nestDestination === null) {
						if (($mapping->flags & Mapping::FLAG_OBJECT_INSTANCE_ONLY) === Mapping::FLAG_OBJECT_INSTANCE_ONLY) {
							continue;
						}

						$nestDestination = null;

						if ($isArrayObject) {
							$nestDestination = [];
						} elseif (class_exists($nestTypeName)) {
							$nestDestination = new $nestTypeName();
						}

						$property->setValue($destination, $nestDestination);
					}

					if ($isArrayObject) {
						$isListArray = ($mapping->flags & Mapping::FLAG_LIST_ARRAY_VALUES) === Mapping::FLAG_LIST_ARRAY_VALUES;
						foreach ($sourceValue as $key => $value) {
							$objClassName = $mapping->arrayValueClassName;
							$obj = new $objClassName();
							$this->mapping($value, $obj);
							if ($isListArray) {
								$nestDestination[] = $obj;
							} else {
								$nestDestination[$key] = $obj;
							}
						}
						$property->setValue($destination, $nestDestination);
					} else {
						$this->mapping($sourceValue, $nestDestination);
					}

					continue 2; // loop: $properties
				}

				if (!Text::isNullOrWhiteSpace($mapping->converter)) {
					/** @phpstan-var TypeConverterBase<mixed> */
					$converter = new $mapping->converter();
					$convertedValue = $converter->read($keyName, $propertyType, $sourceValue);
					$property->setValue($destination, $convertedValue);
					continue 2; // loop: $properties
				}
			}

			// 型一致せずにここまで来たので可能な限り元の型に合わせる
			if (($mapping->flags & Mapping::FLAG_EXCEPTION_TYPE_MISMATCH) === Mapping::FLAG_EXCEPTION_TYPE_MISMATCH) {
				// ただし型変換失敗例外指定の場合は全部諦める
				throw new MapperTypeException($keyName . '(' . $sourceType . '): ' . Text::join('|', Arr::map($propertyTypes, fn(ReflectionNamedType $p) => $p->getName())));
			}

			foreach ($propertyTypes as $propertyType) {
				// 常識的な型だけに限定(独断と偏見)
				switch ($propertyType->getName()) {
					case TypeUtility::TYPE_INTEGER:
					case TypeUtility::TYPE_STRING:
					case TypeUtility::TYPE_BOOLEAN:
					case TypeUtility::TYPE_DOUBLE:
					case TypeUtility::TYPE_NULL:
						if (settype($sourceValue, $propertyType->getName())) {
							$property->setValue($destination, $sourceValue);
							continue 2; // loop: $properties
						}
						break;

					default:
						break;
				}
			}
		}
	}

	public function export(object $source): array
	{
		throw new NotImplementedException('いるか？');
	}

	#endregion
}
