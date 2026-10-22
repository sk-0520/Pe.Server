<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute;

use PeServer\Core\Mvc\Template\Node\Attribute\BuildName;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\AttributeNames;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\BuildAttributeNameOnly;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\DataAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\AutoCapitalize;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\AutoCorrect;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\ControlsListValues;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Bcp47;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\ContentEditable;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\ControlsList as ControlsList;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\CrossOrigin;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Direction;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\InputMode;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Loading;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Popover;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Preload;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Translate;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\VirtualKeyboardPolicy;
use PeServer\Core\Mvc\Template\Node\Html\HtmlDeprecated;

class HTMLAudioAttributes extends HtmlGlobalAttributes
{
	/** @SuppressWarnings("php:S107") */
	public function __construct(
		#[BuildAttributeNameOnly()]
		#[BuildName("autoplay")]
		readonly bool|null $autoPlay = null,
		#[BuildAttributeNameOnly()]
		readonly bool|null $controls = null,
		#[BuildName("controlslist")]
		readonly ControlsList|ControlsListValues|null $controlsList = null,
		#[BuildName(AttributeNames::CROSS_ORIGIN)]
		readonly CrossOrigin|null $crossOrigin = null,
		#[BuildAttributeNameOnly()]
		#[BuildName("disableremoteplayback")]
		readonly bool|null $disableRemotePlayback = null,
		#[HtmlDeprecated(HtmlDeprecated::REASON_EXPERIMENTAL)]
		readonly Loading|null $loading = null,
		#[BuildAttributeNameOnly()]
		public bool|null $loop = null,
		#[BuildAttributeNameOnly()]
		public bool|null $muted = null,
		readonly Preload|null $preload = null,
		/** @var non-empty-string|null */
		readonly string|null $src = null,
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
