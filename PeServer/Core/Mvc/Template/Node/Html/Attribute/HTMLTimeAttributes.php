<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute;

use PeServer\Core\Mvc\Template\Node\Attribute\BuildAttributeBool;
use PeServer\Core\Mvc\Template\Node\Attribute\BuildName;
use PeServer\Core\Mvc\Template\Node\Attribute\Value\StringValues;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\DataAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\AutoCapitalize;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\AutoCorrect;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\AutoComplete;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\ShadowRootMode;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Bcp47;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\ContentEditable;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\DateMonthValue;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\DateOnlyValue;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\DateTimeValue;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\DateTimeValueBase;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Direction;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\InputMode;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Popover;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Translate;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\VirtualKeyboardPolicy;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Wrap;

class HTMLTimeAttributes extends HtmlGlobalAttributes
{
	/** @SuppressWarnings("php:S107") */
	public function __construct(
		public readonly DateTimeValueBase|null $dateTime = null,
		#region HtmlGlobalAttributes
		string|null $accesskey = null,
		string|null $anchor = null,
		AutoCapitalize|null $autoCapitalize = null,
		AutoCorrect|null $autoCorrect = null,
		bool|null $autoFocus = null,
		string|StringValues|null $className = null,
		ContentEditable|null $contentEditable = null,
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

	#region HtmlGlobalAttributes

	#endregion
}
