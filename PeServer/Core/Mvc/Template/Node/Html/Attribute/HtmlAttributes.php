<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute;

use PeServer\Core\Collections\Arr;
use PeServer\Core\Mvc\Template\Node\AttributesBase;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Autocapitalize;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Autocorrect;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Contenteditable;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Direction;
use PeServer\Core\Mvc\Template\Node\AttributeValue;
use PeServer\Core\ReflectionUtility;
use ReflectionClass;
use ReflectionProperty;
use Iterator;

//TODO: Data-*

class HtmlAttributes extends AttributesBase
{
	public function __construct(
		public readonly string|AttributeValue $accesskey = AttributeValue::Unspecified,
		/**
		 * anchor はグローバル属性で、位置指定要素とアンカー要素を関連付けるために使用されます。この属性の値は、位置指定要素をアンカーする要素の id の値です。その後、 CSS アンカー位置指定を使用して要素の位置指定を行うことができます。
		 *
		 * #[Deprecated] を使いたかったけど周りのツールがうるさかった
		 *
		 * @deprecated
		 * @see https://developer.mozilla.org/docs/Web/HTML/Reference/Global_attributes/anchor
		 */
		public readonly string|AttributeValue $anchor = AttributeValue::Unspecified,
		public readonly Autocapitalize|AttributeValue $autocapitalize = AttributeValue::Unspecified,
		public readonly Autocorrect|AttributeValue $autocorrect = AttributeValue::Unspecified,
		public readonly bool|AttributeValue $autofocus  = AttributeValue::Unspecified,
		public readonly string|AttributeValue $class = AttributeValue::Unspecified,
		public readonly Contenteditable|AttributeValue $contenteditable = AttributeValue::Unspecified,
		public readonly Direction|AttributeValue $dir = AttributeValue::Unspecified,
		public readonly bool|AttributeValue $draggable = AttributeValue::Unspecified,
		public readonly string|AttributeValue $enterkeyhint = AttributeValue::Unspecified,
		public readonly bool|string|AttributeValue $hidden = AttributeValue::Unspecified,
	) {
		//NOP
	}

	#region function



	#endregion

	#region AttributesBase

	public function getNames(): Iterator
	{
		$reflection = new ReflectionClass($this);
		$properties = ReflectionUtility::getAllProperties($reflection, ReflectionProperty::IS_PUBLIC);
		foreach ($properties as $property) {
			$value = $property->getRawValue($this);
			if ($value === AttributeValue::Unspecified) {
				continue;
			}
			yield $property->getName();
		}
	}

	public function buildName(string $name): string
	{
		throw new \PeServer\Core\Throws\NotImplementedException();
	}


	#endregion
}
