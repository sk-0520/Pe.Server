<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute;

use PeServer\Core\Mvc\Template\Node\Attribute\BuildName;
use PeServer\Core\Mvc\Template\Node\Attribute\Value\IAttributeValue;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\DataAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\AutoCapitalize;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\AutoCorrect;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Bcp47;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\ButtonType;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Command;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\ContentEditable;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Direction;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\FormEncType;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\FormMethod;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\FormTarget;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\InputMode;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Popover;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\PopoverTargetAction;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Translate;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\VirtualKeyboardPolicy;
use PeServer\Core\Mvc\Template\Node\Html\HtmlDeprecated;

class HTMLButtonAttributes extends HtmlGlobalAttributes
{
	/** @SuppressWarnings("php:S107") */
	public function __construct(
		/** @var Command|non-empty-string|null */
		public readonly Command|string|null $command = null,
		/** @var non-empty-string|null */
		#[BuildName("commandfor")]
		public readonly string|null $commandFor = null,
		#[BuildAttributeNameOnly()]
		public readonly bool|null $disabled = null,
		/** @var non-empty-string|null */
		public readonly string|null $form = null,
		/** @var non-empty-string|null */
		#[BuildName("formaction")]
		public readonly string|null $formAction = null,
		/** @var FormEncType|null */
		#[BuildName("formenctype")]
		public readonly FormEncType|null $formEncType = null,
		/** @var FormMethod|null */
		#[BuildName("formmethod")]
		public readonly FormMethod|null $formMethod = null,
		#[BuildAttributeNameOnly()]
		#[BuildName("formnovalidate")]
		public readonly bool|null $formNoValidate = null,
		/** @var FormTarget|non-empty-string|null */
		#[BuildName("formtarget")]
		public readonly FormTarget|string|null $formTarget = null,
		#[BuildName("interestfor")]
		#[HtmlDeprecated(HtmlDeprecated::REASON_NON_STANDARD | HtmlDeprecated::REASON_EXPERIMENTAL)]
		public readonly bool|null $interestFor = null,
		/** @var non-empty-string|null */
		public readonly string|null $name = null,
		#[BuildName("popovertarget")]
		public readonly string|null $popoverTarget = null,
		#[BuildName("popovertargetaction")]
		public readonly PopoverTargetAction|null $popoverTargetAction = null,
		public readonly ButtonType|null $type = null,
		public readonly string|int|float|bool|IAttributeValue|null $value = null,

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
