<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper;

use Override;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTextAreaAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputTextAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputUrlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Components\ShowErrorMessages\ShowErrorMessages;
use PeServer\Core\Mvc\Template\Node\Html\Components\ShowErrorMessages\ShowErrorMessagesProps;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentTrait;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\TextNode;
use PeServer\Core\Text;

/**
 * @extends HtmlComponentBase<InputHelperProps>
 */
class InputHelper extends HtmlComponentBase
{
	/** @template-use HtmlComponentTrait<InputHelperProps> */
	use HtmlComponentTrait;

	#[Override]
	protected function getNode(): INode
	{
		$key = $this->props->key;
		$value = Text::toString($this->props->parameter->values[$key] ?? Text::EMPTY);
		$className = $this->props->className;
		if (!empty($this->props->parameter->errors[$key])) {
			$className = Text::isNullOrWhiteSpace($className) ? 'error' : $className . ' error';
		}

		$input = match ($this->props->type) {
			InputHelperType::Text => $this->context->builder->inputText(new HTMLInputTextAttributes(
				id: $key,
				name: $key,
				value: $value,
				className: $className,
				required: $this->props->required,
				data: $this->props->data,
			)),
			InputHelperType::Url => $this->context->builder->inputUrl(new HTMLInputUrlAttributes(
				id: $key,
				name: $key,
				value: $value,
				className: $className,
				required: $this->props->required,
				data: $this->props->data,
			)),
			InputHelperType::Textarea => $this->context->builder->textarea(
				new HTMLTextAreaAttributes(
					id: $key,
					name: $key,
					className: $className,
					required: $this->props->required,
					data: $this->props->data,
				),
				new TextNode($value)
			),
		};

		return new Fragment([
			$input,
			$this->props->autoError
				? new ShowErrorMessages(
					new ShowErrorMessagesProps($this->props->parameter->errors, $key),
					$this->context
				)
				: null,
		]);
	}
}
