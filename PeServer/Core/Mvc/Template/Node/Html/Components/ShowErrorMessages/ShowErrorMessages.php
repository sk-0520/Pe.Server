<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Components\ShowErrorMessages;

use Override;
use PeServer\Core\Collections\Arr;
use PeServer\Core\I18n;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Logic\Validator;
use PeServer\Core\Mvc\Template\Node\Attribute\Value\StringValues;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLiAttributes;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentTrait;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\NoneNode;
use PeServer\Core\Mvc\Template\Node\TextNode;

/**
 * @extends HtmlComponentBase<\PeServer\Core\Mvc\Template\Node\Html\Components\ShowErrorMessages\ShowErrorMessagesProps>
 */
class ShowErrorMessages extends HtmlComponentBase
{
	/** @template-use HtmlComponentTrait<\PeServer\Core\Mvc\Template\Node\Html\Components\ShowErrorMessages\ShowErrorMessagesProps> */
	use HtmlComponentTrait;

	#region HtmlComponentBase

	#[Override]
	protected function getNode(): INode
	{
		if (Arr::isNullOrEmpty($this->props->errors)) {
			return NoneNode::get();
		}

		$messages = $this->props->errors[$this->props->key] ?? [];
		if ($this->props->key !== Validator::COMMON && Arr::isNullOrEmpty($messages)) {
			return NoneNode::get();
		}

		$classes = ['errors'];
		if ($this->props->key === Validator::COMMON) {
			$classes[] = 'common-error';
		} else {
			$classes[] = 'value-error';
		}

		$ulElement = $this->context->builder->ul(
			new HtmlGlobalAttributes(
				className: new StringValues($classes)
			),
			new Fragment(
				Arr::map(
					$messages,
					fn($a) => $this->context->builder->li(
						new HTMLLiAttributes(
							className: "error"
						),
						new TextNode($a)
					)
				)
			)
		);

		if ($this->props->key !== Validator::COMMON) {
			return $ulElement;
		}

		return $this->context->builder->div(
			new HtmlGlobalAttributes(
				className: new StringValues(["common", "error"])
			),
			new Fragment(
				[
					$this->context->builder->p(
						new HtmlGlobalAttributes(),
						new TextNode(I18n::message(I18n::COMMON_ERROR_TITLE))
					),
					Arr::isNullOrEmpty($messages) ? NoneNode::get() : $ulElement
				]
			)
		);
	}

	#endregion
}
