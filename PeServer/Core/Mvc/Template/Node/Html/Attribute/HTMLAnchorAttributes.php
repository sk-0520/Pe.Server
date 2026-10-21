<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute;

use Override;
use PeServer\Core\Mvc\Template\Node\Attribute\BuildName;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\DataAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Autocapitalize;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Autocorrect;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Rel;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Bcp47;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Contenteditable;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Direction;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\InputMode;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Popover;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\ReferrerPolicy;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Target;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Translate;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\VirtualKeyboardPolicy;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\BuildAttributeNameOnly;
use PeServer\Core\Mvc\Template\Node\Html\HtmlDeprecated;
use UnitEnum;

class HTMLAnchorAttributes extends HTMLGlobalAttributes
{
	/** @SuppressWarnings("php:S107") */
	public function __construct(
		#[BuildAttributeNameOnly()]
		public readonly bool|null $download = null,
		public readonly string|null $href = null,
		public readonly Bcp47|null $hreflang = null,
		#[BuildName("interestforeign")]
		#[HtmlDeprecated(HtmlDeprecated::REASON_NON_STANDARD | HtmlDeprecated::REASON_EXPERIMENTAL)]
		public readonly string|null $interestForeign = null,
		public readonly string|null $ping = null,
		public readonly ReferrerPolicy|null $referrerpolicy = null,
		public readonly Rel|null $rel = null,
		/**
		 * @phpstan-var Target|non-empty-string|null
		 */
		public readonly Target|string|null $target = null,
		public readonly string|null $type = null,
		#region HTMLGlobalAttributes
		string|null $accesskey = null,
		string|null $anchor = null,
		Autocapitalize|null $autoCapitalize = null,
		Autocorrect|null $autoCorrect = null,
		bool|null $autoFocus = null,
		string|null $className = null,
		Contenteditable|null $contentEditable = null,
		DataAttributes|null $data = null,
		Direction|null $dir = null,
		bool|null $draggable = null,
		string|null $enterKeyHint = null,
		bool|string|null $hidden = null,
		string|null $id = null,
		bool|null $inert = null,
		InputMode|null $inputMode = null,
		string|null $is = null,
		string|null $itemId = null,
		string|null $itemProp = null,
		string|null $itemRef = null,
		string|null $itemType = null,
		Bcp47|null $lang = null,
		string|null $nonce = null,
		string|null $part = null,
		Popover|null $popover = null,
		string|null $role = null,
		string|null $slot = null,
		bool|null $spellcheck = null,
		string|null $style = null,
		int|null $tabIndex = null,
		string|null $title = null,
		Translate|null $translate = null,
		VirtualKeyboardPolicy|null $virtualKeyboardPolicy = null,
		bool|null $writingSuggestions = null,
		#endregion
	) {
		parent::__construct(
			accesskey: $accesskey,
			anchor: $anchor,
			autoCapitalize: $autoCapitalize,
			autoCorrect: $autoCorrect,
			autoFocus: $autoFocus,
			className: $className,
			contentEditable: $contentEditable,
			data: $data,
			dir: $dir,
			draggable: $draggable,
			enterKeyHint: $enterKeyHint,
			hidden: $hidden,
			id: $id,
			inert: $inert,
			inputMode: $inputMode,
			is: $is,
			itemId: $itemId,
			itemProp: $itemProp,
			itemRef: $itemRef,
			itemType: $itemType,
			lang: $lang,
			nonce: $nonce,
			part: $part,
			popover: $popover,
			role: $role,
			slot: $slot,
			spellcheck: $spellcheck,
			style: $style,
			tabIndex: $tabIndex,
			title: $title,
			translate: $translate,
			virtualKeyboardPolicy: $virtualKeyboardPolicy,
			writingSuggestions: $writingSuggestions
		);
	}

	#region HTMLGlobalAttributes

	#endregion
}
