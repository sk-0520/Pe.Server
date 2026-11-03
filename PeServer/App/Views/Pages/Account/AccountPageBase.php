<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Account;

use PeServer\App\Views\Components\Pages\Default\DefaultPage;
use PeServer\App\Views\Components\Pages\Default\DefaultPageProps;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLButtonAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLFormAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\FormMethod;
use PeServer\Core\Mvc\Template\Node\Html\Components\Csrf\Csrf;
use PeServer\Core\Mvc\Template\Node\Html\Components\Csrf\CsrfProps;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelper;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelperProps;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelperType;
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
abstract class AccountPageBase extends RootHtmlComponentBase
{
	use RootHtmlComponentTrait;

	protected function layout(string $title, INode $children, string|null $script = null): INode
	{
		return new DefaultPage(new DefaultPageProps(
			parameter: $this->props->parameter,
			title: $title,
			children: $children,
			defaultScriptPath: $script,
		), $this->context);
	}

	/** @param non-empty-string $action */
	protected function form(string $action, string $className, INode $children): INode
	{
		return $this->context->builder->form(
			new HTMLFormAttributes(action: $action, method: FormMethod::Post, className: $className),
			new Fragment([new Csrf(new CsrfProps(), $this->context), $children])
		);
	}

	protected function field(string $label, InputHelperProps $props): INode
	{
		$b = $this->context->builder;
		return new Fragment([
			$b->dt(new HtmlGlobalAttributes(), new TextNode($label)),
			$b->dd(new HtmlGlobalAttributes(), new InputHelper($props, $this->context)),
		]);
	}

	/** @param non-empty-string $key */
	protected function input(string $label, string $key, InputHelperType $type, bool $required = false): INode
	{
		return $this->field($label, new InputHelperProps($this->props->parameter, $key, $type, 'edit', $required));
	}

	protected function submit(string $label): INode
	{
		$b = $this->context->builder;
		return new Fragment([
			$b->dt(new HtmlGlobalAttributes(className: 'action'), NoneNode::get()),
			$b->dd(
                new HtmlGlobalAttributes(className: 'action'),
                $b->button(new HTMLButtonAttributes(), new TextNode($label))
            ),
		]);
	}
}
