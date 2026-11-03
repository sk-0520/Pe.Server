<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Components\Pager;

use Override;
use PeServer\Core\Mvc\PageShortcutKind;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLAnchorAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLiAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentTrait;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\NoneNode;
use PeServer\Core\Mvc\Template\Node\TextNode;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;

/** @extends HtmlComponentBase<PagerProps> */
class Pager extends HtmlComponentBase
{
	/** @template-use HtmlComponentTrait<PagerProps> */
	use HtmlComponentTrait;

	#[Override]
	protected function getNode(): INode
	{
		$shortcuts = $this->props->data->getShortcuts();
		if (empty($shortcuts)) {
			return NoneNode::get();
		}
		if (Text::isNullOrWhiteSpace($this->props->href)) {
			throw new ArgumentException('href');
		}
		$items = [];
		foreach ($shortcuts as $index => $shortcut) {
			$current = $shortcut->kind === PageShortcutKind::Normal && $shortcut->current;
			$label = match ($shortcut->kind) {
				PageShortcutKind::Long => $index === 0 ? $this->props->jumpHead : $this->props->jumpTail,
				PageShortcutKind::Short => $index <= 1 ? $this->props->jumpPrev : $this->props->jumpNext,
				PageShortcutKind::Normal => (string)$shortcut->pageNumber,
			};
			$items[] = $this->context->builder->li(
				new HTMLLiAttributes(className: $shortcut->kind->value . (!$shortcut->enabled ? ' disabled' : '') . ($current ? ' current' : '')),
				$this->context->builder->a(
					new HTMLAnchorAttributes(
						href: $shortcut->enabled ? str_replace('<page_number>', (string)$shortcut->pageNumber, $this->props->href) : null,
						className: !$shortcut->enabled ? 'disabled' : ($current ? 'current' : null),
						tabIndex: $shortcut->enabled ? null : -1,
					),
					new TextNode($label)
				)
			);
		}
		return $this->context->builder->ul(new HtmlGlobalAttributes(className: 'pagination'), new Fragment($items));
	}
}
