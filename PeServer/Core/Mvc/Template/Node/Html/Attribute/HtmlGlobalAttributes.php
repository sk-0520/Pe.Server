<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\DataAttributes;
use PeServer\Core\Mvc\Template\Node\AttributesBase;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Autocapitalize;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Autocorrect;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Contenteditable;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Direction;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\InputMode;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Popover;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Translate;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\VirtualKeyboardPolicy;
use PeServer\Core\ReflectionUtility;
use ReflectionClass;
use ReflectionProperty;
use Iterator;
use Override;
use PeServer\Core\Mvc\Template\Node\AttributeValueMark;

//TODO: Data-*

class HtmlGlobalAttributes extends AttributesBase
{
	public function __construct(
		public readonly string|null $accesskey = null,
		/**
		 * anchor はグローバル属性で、位置指定要素とアンカー要素を関連付けるために使用されます。この属性の値は、位置指定要素をアンカーする要素の id の値です。その後、 CSS アンカー位置指定を使用して要素の位置指定を行うことができます。
		 *
		 * #[Deprecated] を使いたかったけど周りのツールがうるさかった
		 *
		 * @deprecated
		 * @see https://developer.mozilla.org/docs/Web/HTML/Reference/Global_attributes/anchor
		 */
		public readonly string|null $anchor = null,
		public readonly Autocapitalize|null $autocapitalize = null,
		public readonly Autocorrect|null $autocorrect = null,
		public readonly bool|null $autofocus = null,
		public readonly string|null $class = null,
		public readonly Contenteditable|null $contenteditable = null,
		public readonly DataAttributes|null $data = null,
		public readonly Direction|null $dir = null,
		public readonly bool|null $draggable = null,
		public readonly string|null $enterkeyhint = null,
		public readonly bool|string|null $hidden = null,
		public readonly string|null $id = null,
		public readonly bool|null $inert = null,
		public readonly InputMode|null $inputmode = null,
		public readonly string|null $is = null,
		public readonly string|null $itemid = null,
		public readonly string|null $itemprop = null,
		public readonly string|null $itemref = null,
		public readonly string|null $itemtype = null,
		public readonly string|null $lang = null,
		public readonly string|null $nonce = null,
		public readonly string|null $part = null,
		public readonly Popover|null $popover = null,
		public readonly string|null $role = null,
		public readonly string|null $slot = null,
		public readonly bool|null $spellcheck = null,
		public readonly string|null $style = null,
		public readonly int|null $tabindex = null,
		public readonly string|null $title  = null,
		public readonly Translate|null $translate = null,
		public readonly VirtualKeyboardPolicy|null $virtualkeyboardpolicy = null,
		public readonly bool|null $writingsuggestions  = null,
	) {
		//NOP
	}

	#region function



	#endregion

	#region AttributesBase

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
			yield $property->getName();
		}
	}

	#[Override]
	public function build(string $name, AttributeValueMark $mark = AttributeValueMark::DoubleQuote): string
	{
		throw new \PeServer\Core\Throws\NotImplementedException();
	}


	#endregion
}
