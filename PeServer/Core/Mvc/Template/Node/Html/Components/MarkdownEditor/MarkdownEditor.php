<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Components\MarkdownEditor;

use Override;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\DataAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLabelAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputRadioAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelper;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelperProps;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelperType;
use PeServer\Core\Mvc\Template\Node\Html\Components\Markdown\Markdown;
use PeServer\Core\Mvc\Template\Node\Html\Components\Markdown\MarkdownProps;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentTrait;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\TextNode;
use PeServer\Core\Text;

/** @extends HtmlComponentBase<MarkdownEditorProps> */
class MarkdownEditor extends HtmlComponentBase
{
	/** @template-use HtmlComponentTrait<MarkdownEditorProps> */
	use HtmlComponentTrait;

	#[Override]
	protected function getNode(): INode
	{
		$b = $this->context->builder;
		return $b->div(new HtmlGlobalAttributes(className: 'tab'), new Fragment([
			$b->inputRadio(new HTMLInputRadioAttributes(id: 'description_markdown_source', name: 'tab_markdown', className: 'tab_check', checked: true)),
			$b->label(new HTMLLabelAttributes(htmlFor: 'description_markdown_source', className: 'tab_header'), new TextNode('Markdown')),
			$b->div(new HtmlGlobalAttributes(className: 'tab_content'), new InputHelper(new InputHelperProps(
				parameter: $this->props->parameter,
				key: $this->props->key,
				type: InputHelperType::Textarea,
				className: 'edit markdown-editor',
				data: new DataAttributes(['markdown-result' => '.markdown-browser']),
			), $this->context)),
			$b->inputRadio(new HTMLInputRadioAttributes(id: 'description_markdown_preview', name: 'tab_markdown', className: 'tab_check')),
			$b->label(new HTMLLabelAttributes(htmlFor: 'description_markdown_preview', className: 'tab_header'), new TextNode('プレビュー')),
			$b->div(new HtmlGlobalAttributes(className: 'tab_content'), new Markdown(new MarkdownProps(
				content: Text::toString($this->props->parameter->values[$this->props->key] ?? Text::EMPTY),
				className: 'markdown markdown-browser',
			), $this->context)),
		]));
	}
}
