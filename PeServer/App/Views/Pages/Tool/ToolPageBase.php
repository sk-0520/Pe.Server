<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Tool;

use PeServer\App\Views\Components\Pages\Default\DefaultPage;
use PeServer\App\Views\Components\Pages\Default\DefaultPageProps;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\DataAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLButtonAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLFormAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLabelAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLiAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputRadioAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\FormMethod;
use PeServer\Core\Mvc\Template\Node\Html\Components\Csrf\Csrf;
use PeServer\Core\Mvc\Template\Node\Html\Components\Csrf\CsrfProps;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelper;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelperProps;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelperType;
use PeServer\Core\Mvc\Template\Node\Html\Components\ShowErrorMessages\ShowErrorMessages;
use PeServer\Core\Mvc\Template\Node\Html\Components\ShowErrorMessages\ShowErrorMessagesProps;
use PeServer\Core\Mvc\Template\Node\Html\RootHtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\Html\RootHtmlComponentTrait;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\NoneNode;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\Node\TextNode;

/**
 * @template TRootProps of RootProps
 * @extends RootHtmlComponentBase<TRootProps>
 */
abstract class ToolPageBase extends RootHtmlComponentBase
{
	use RootHtmlComponentTrait;

	protected function layout(string $title, INode $children): INode
	{
		return new DefaultPage(new DefaultPageProps(
			parameter: $this->props->parameter,
			title: $title,
			children: $children,
		), $this->context);
	}

	/**
	 * @phpstan-param non-empty-string $action
	 * @phpstan-param non-empty-string $inputKey
	 * @phpstan-param non-empty-string $kindKey
	 * @phpstan-param array<non-empty-string, string> $choices
	 */
	protected function form(string $action, string $inputKey, string $kindKey, array $choices): INode
	{
		$b = $this->context->builder;
		$choiceNodes = [];
		$selected = $this->props->parameter->values[$kindKey] ?? null;
		foreach ($choices as $value => $label) {
			$choiceNodes[] = $b->li(
				new HTMLLiAttributes(),
				$b->label(new HTMLLabelAttributes(), new Fragment([
					$b->inputRadio(new HTMLInputRadioAttributes(
						name: $kindKey,
						value: $value,
						checked: $selected === $value,
					)),
					new TextNode($label),
				]))
			);
		}

		return $b->form(
			new HTMLFormAttributes(action: $action, method: FormMethod::Post),
			new Fragment([
				new Csrf(new CsrfProps(), $this->context),
				$b->dl(new HtmlGlobalAttributes(className: 'input'), new Fragment([
					$b->dt(new HtmlGlobalAttributes(), new TextNode('input')),
					$b->dd(new HtmlGlobalAttributes(), new InputHelper(new InputHelperProps(
						$this->props->parameter,
						$inputKey,
						InputHelperType::Textarea,
						'edit',
						true
					), $this->context)),
					$b->dt(new HtmlGlobalAttributes(), new TextNode('type')),
					$b->dd(new HtmlGlobalAttributes(), new Fragment([
						$b->ul(new HtmlGlobalAttributes(className: 'inline'), new Fragment($choiceNodes)),
						new ShowErrorMessages(new ShowErrorMessagesProps($this->props->parameter->errors, $kindKey), $this->context),
					])),
					$b->dt(new HtmlGlobalAttributes(className: 'action'), NoneNode::get()),
					$b->dd(new HtmlGlobalAttributes(className: 'action'), $b->button(new HTMLButtonAttributes(), new TextNode('submit'))),
				])),
			])
		);
	}

	protected function warning(): INode
	{
		return $this->context->builder->p(
			new HtmlGlobalAttributes(),
			$this->context->builder->strong(new HtmlGlobalAttributes(), new TextNode('本処理はサーバーを経由する。'))
		);
	}

	protected function result(string $key): ?INode
	{
		if (empty($this->props->parameter->values['has_result'])) {
			return null;
		}

		$value = strval($this->props->parameter->values[$key]);
		return $this->context->builder->pre(
			new HtmlGlobalAttributes(
				data: new DataAttributes(['clipboard' => 'block']),
				style: 'white-space: pre-wrap;'
			),
			new TextNode($value)
		);
	}
}
