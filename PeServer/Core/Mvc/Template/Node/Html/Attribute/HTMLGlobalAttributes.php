<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute;

use Iterator;
use Override;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Mvc\Template\Node\Attribute\AttributesBase;
use PeServer\Core\Mvc\Template\Node\Attribute\AttributeValueMark;
use PeServer\Core\Mvc\Template\Node\Attribute\Value\IAttributeValue;
use PeServer\Core\Mvc\Template\Node\Attribute\BuildName;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\DataAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Autocapitalize;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Autocorrect;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Bcp47;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Contenteditable;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Direction;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\InputMode;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Popover;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Translate;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\VirtualKeyboardPolicy;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\BuildAttributeNameOnly;
use PeServer\Core\Mvc\Template\Node\Html\HtmlDeprecated;
use PeServer\Core\ReflectionUtility;
use PeServer\Core\Text;
use ReflectionClass;
use ReflectionProperty;
use UnitEnum;

class HTMLGlobalAttributes extends AttributesBase
{
	/** @SuppressWarnings("php:S107") */
	public function __construct(
		public readonly string|null $accesskey = null,
		/**
		 * anchor はグローバル属性で、位置指定要素とアンカー要素を関連付けるために使用されます。この属性の値は、位置指定要素をアンカーする要素の id の値です。その後、 CSS アンカー位置指定を使用して要素の位置指定を行うことができます。
		 *
		 * @see https://developer.mozilla.org/docs/Web/HTML/Reference/Global_attributes/anchor
		 */
		#[HtmlDeprecated(HtmlDeprecated::REASON_OBSOLETE | HtmlDeprecated::REASON_EXPERIMENTAL)]
		public readonly string|null $anchor = null,
		#[BuildName('autocapitalize')]
		public readonly Autocapitalize|null $autoCapitalize = null,
		#[BuildName('autocorrect')]
		public readonly Autocorrect|null $autoCorrect = null,
		#[BuildName('autofocus')]
		public readonly bool|null $autoFocus = null,
		#[BuildName('class')]
		public readonly string|null $className = null,
		#[BuildName('contenteditable')]
		public readonly Contenteditable|null $contentEditable = null,
		public readonly DataAttributes|null $data = null,
		public readonly Direction|null $dir = null,
		public readonly bool|null $draggable = null,
		#[BuildName('enterkeyhint')]
		public readonly string|null $enterKeyHint = null,
		public readonly bool|string|null $hidden = null,
		public readonly string|null $id = null,
		public readonly bool|null $inert = null,
		#[BuildName('inputmode')]
		public readonly InputMode|null $inputMode = null,
		public readonly string|null $is = null,
		#[BuildName('itemid')]
		public readonly string|null $itemId = null,
		#[BuildName('itemprop')]
		public readonly string|null $itemProp = null,
		#[BuildName('itemref')]
		public readonly string|null $itemRef = null,
		#[BuildName('itemtype')]
		public readonly string|null $itemType = null,
		public readonly Bcp47|null $lang = null,
		public readonly string|null $nonce = null,
		public readonly string|null $part = null,
		public readonly Popover|null $popover = null,
		public readonly string|null $role = null,
		public readonly string|null $slot = null,
		public readonly bool|null $spellcheck = null,
		public readonly string|null $style = null,
		#[BuildName('tabindex')]
		public readonly int|null $tabIndex = null,
		public readonly string|null $title = null,
		public readonly Translate|null $translate = null,
		#[BuildName('virtualkeyboardpolicy')]
		public readonly VirtualKeyboardPolicy|null $virtualKeyboardPolicy = null,
		#[BuildName('writingsuggestions')]
		public readonly bool|null $writingSuggestions = null,
	) {
		//NOP
	}

	#region function

	protected function getNameOnlyAttribute(ReflectionProperty $property, bool $value): BuildAttributeNameOnly|null
	{
		$attributes = $property->getAttributes(BuildAttributeNameOnly::class);
		if (!Arr::isNullOrEmpty($attributes)) {
			return $attributes[0]->newInstance();
		}

		return null;
	}

	#endregion

	#region AttributesBase

	protected function buildCommon(string $name, string|int|float|bool|UnitEnum|IAttributeValue $value, AttributeValueMark $mark): string
	{
		if (property_exists($this, $name)) {
			$property = new ReflectionProperty($this, $name);

			if (is_bool($value)) {
				$nameOnlyAttribute = $this->getNameOnlyAttribute($property, $value);
				if ($nameOnlyAttribute !== null) {
					$showName = $nameOnlyAttribute->condition === $value;
					$workName = $this->buildNameByReflection($property);
					return $showName ? ($workName ?? $name) : '';
				}
			}
		}

		return parent::buildCommon($name, $value, $mark);
	}

	#[Override]
	public function getNames(): Iterator
	{
		$reflection = new ReflectionClass($this);
		$properties = ReflectionUtility::getAllProperties($reflection, ReflectionProperty::IS_PUBLIC);
		foreach ($properties as $property) {
			$value = $property->getRawValue($this);

			if ($value === null) {
				continue;
			}
			if ($value instanceof DataAttributes && $value->isEmpty()) {
				continue;
			}

			yield $property->getName();
		}
	}

	#[Override]
	public function build(string $name, AttributeValueMark $mark = AttributeValueMark::DoubleQuote): string
	{
		if ($name === 'data' && $this->data !== null) {
			$dataResult = [];
			foreach ($this->data->getNames() as $names) {
				$dataResult[] = $this->data->build($names, $mark);
			}
			return Text::join(' ', $dataResult);
		}

		return $this->buildCommon($name, $this->$name, $mark);
	}


	#endregion
}
