<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Management;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use PeServer\App\Views\Components\Pages\Default\DefaultPage;
use PeServer\App\Views\Components\Pages\Default\DefaultPageProps;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLAnchorAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLButtonAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLFormAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTimeAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\DateTimeValue;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\FormEncType;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\FormMethod;
use PeServer\Core\Mvc\Template\Node\Html\Components\Csrf\Csrf;
use PeServer\Core\Mvc\Template\Node\Html\Components\Csrf\CsrfProps;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelper;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelperProps;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelperType;
use PeServer\Core\Mvc\Template\Node\Html\RootHtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\Html\RootHtmlComponentTrait;
use PeServer\Core\Mvc\Template\Node\Html\ScriptSourceNodeBase;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\Node\TextNode;

/**
 * @template TRootProps of RootProps
 * @extends RootHtmlComponentBase<TRootProps>
 */
abstract class ManagementPageBase extends RootHtmlComponentBase
{
	use RootHtmlComponentTrait;

	protected function layout(string $title, INode $children, ?string $script = null, ?INode $head = null, ?ScriptSourceNodeBase $scriptCode = null): INode
	{
		return new DefaultPage(new DefaultPageProps(
			parameter: $this->props->parameter,
			title: $title,
			children: $children,
			defaultScriptPath: $script,
			customHeadNode: $head,
			customScriptCode: $scriptCode,
		), $this->context);
	}

	/** @param non-empty-string|null $action */
	protected function form(?string $action, INode $children, string $className = '', ?FormEncType $encType = null, bool $csrf = true): INode
	{
		return $this->context->builder->form(
			new HTMLFormAttributes(action: $action, method: FormMethod::Post, className: $className, encType: $encType),
			new Fragment([$csrf ? new Csrf(new CsrfProps(), $this->context) : null, $children])
		);
	}

	/** @param non-empty-string $href */
	protected function link(string $href, string $label): INode
	{
		return $this->context->builder->a(new HTMLAnchorAttributes(href: $href), new TextNode($label));
	}

	protected function field(string $label, INode|string $content): INode
	{
		$b = $this->context->builder;
		return new Fragment([
			$b->dt(new HtmlGlobalAttributes(), new TextNode($label)),
			$b->dd(new HtmlGlobalAttributes(), is_string($content) ? new TextNode($content) : $content),
		]);
	}

	/** @param non-empty-string $key */
	protected function input(string $label, string $key, InputHelperType $type, bool $required = false): INode
	{
		return $this->field($label, new InputHelper(new InputHelperProps($this->props->parameter, $key, $type, 'edit', $required), $this->context));
	}

	protected function submit(string $label, string $heading = ''): INode
	{
		$b = $this->context->builder;
		return new Fragment([
			$b->dt(new HtmlGlobalAttributes(className: 'action'), new TextNode($heading)),
			$b->dd(new HtmlGlobalAttributes(className: 'action'), $b->button(new HTMLButtonAttributes(), new TextNode($label))),
		]);
	}

	protected function timestamp(DateTimeInterface $value, string $format = 'Y-m-d\\TH:i:sP'): INode
	{
		$display = DateTimeImmutable::createFromInterface($value)->setTimezone(new DateTimeZone('Asia/Tokyo'));
		return $this->context->builder->time(
			new HTMLTimeAttributes(dateTime: new DateTimeValue($value)),
			new TextNode($display->format($format))
		);
	}
}
