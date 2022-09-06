<?php

declare(strict_types=1);

namespace PeServer\Core\Serialization;

use PeServer\Core\Collections\Arr;
use PeServer\Core\DefaultValue;
use PeServer\Core\ReflectionUtility;
use PeServer\Core\Text;
use PeServer\Core\Throws\KeyNotFoundException;
use PeServer\Core\Throws\MapperKeyNotFoundException;
use PeServer\Core\Throws\MapperTypeException;
use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\TypeUtility;
use ReflectionClass;
use ReflectionNamedType;
use TypeError;

/**
 * 配列とクラスオブジェクトを相互変換。
 *
 * * 配列とクラスの設計は十分に制御可能なデータであることが前提(=開発者が操作可能)
 */
class Mapper
{
	#region variable

	/**
	 * 人畜無害なマッピング設定。
	 *
	 * @var Mapping
	 * @readonly
	 */
	private Mapping $defaultMapping;

	#endregion

	public function __construct()
	{
		$this->defaultMapping = new Mapping(Text::EMPTY, Mapping::FLAG_NONE);
	}

	#region function

	/**
	 * 配列データをオブジェクトにマッピング。
	 *
	 * @param array<string,mixed> $source 元データ。
	 * @param object $destination マッピング先
	 * @throws MapperKeyNotFoundException キーが見つからない(`Mapping::FLAG_EXCEPTION_NOT_FOUND_KEY`)。
	 * @throws MapperTypeException 型変換がもう無理(`Mapping::FLAG_EXCEPTION_TYPE_MISMATCH`)。
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 */
	public function mapping(array $source, object $destination): void
	{
		$destReflection = new ReflectionClass($destination);
		$properties = ReflectionUtility::getAllProperties($destReflection);

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

			$sourceValue = $source[$keyName];
			unset($source[$keyName]); //対象キーは不要なので破棄
			$sourceType = TypeUtility::getType($sourceValue);
			$propertyTypes = ReflectionUtility::getTypes($property->getType());
			foreach ($propertyTypes as $propertyType) {
				$nestTypeName = $propertyType->getName();
				$isArrayObject = $nestTypeName === TypeUtility::TYPE_ARRAY && class_exists($mapping->arrayValueClassName);

				if ($sourceType === $nestTypeName && !$isArrayObject) {
					$property->setValue($destination, $sourceValue);
					continue 2; // loop: $properties
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
						} else if (class_exists($nestTypeName)) {
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
			}

			// 型一致せずにここまで来たので可能な限り元の型に合わせる
			if (($mapping->flags & Mapping::FLAG_EXCEPTION_TYPE_MISMATCH) === Mapping::FLAG_EXCEPTION_TYPE_MISMATCH) {
				// ただし型変換失敗例外指定の場合は全部諦める
				throw new MapperTypeException($keyName . '(' . $sourceType . '): ' . Text::join('|', Arr::map($propertyTypes, fn (ReflectionNamedType $p) => $p->getName())));
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

					default:
						break;
				}
			}
		}
	}

	/**
	 * オブジェクトデータを配列に変換。
	 *
	 * @param object $source
	 * @return array<string,mixed>
	 */
	public function export(object $source): array
	{
		throw new NotImplementedException('いるか？');
	}

	#endregion
}
