<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute;

use PeServer\Core\Mvc\Template\Node\Attribute\BuildName;
use PeServer\Core\Mvc\Template\Node\Attribute\Value\IAttributeValue;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\DataAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\AutoCapitalize;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\LinkAs;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\FetchPriority;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Rel;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\AutoCorrect;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Bcp47;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\ContentEditable;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\DateTimeValue;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Direction;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\InputMode;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Popover;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\ReferrerPolicy;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Target;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Translate;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\VirtualKeyboardPolicy;

class HTMLOutputAttributes extends HtmlGlobalAttributes
{
	/** @SuppressWarnings("php:S107") */
	public function __construct(
		#[BuildName("for")]
		public readonly string|null $htmlFor = null,
		/** @var non-empty-string|null */
		public readonly string|null $form = null,
		/** @var non-empty-string|null */
		public readonly string|null $name = null,

		#region HtmlGlobalAttributes
		string|null $accesskey = null,
		string|null $anchor = null,
		AutoCapitalize|null $autoCapitalize = null,
		AutoCorrect|null $autoCorrect = null,
		bool|null $autoFocus = null,
		string|null $className = null,
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
