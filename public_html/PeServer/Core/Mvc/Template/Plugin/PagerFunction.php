<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Plugin;

use Exception;
use PeServer\Core\Code;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Html\HtmlDocument;
use PeServer\Core\Mvc\PageShortcut;
use PeServer\Core\Mvc\PageShortcutKind;
use PeServer\Core\Mvc\Pagination;
use PeServer\Core\Mvc\Template\Plugin\TemplateFunctionBase;
use PeServer\Core\Text;

class PagerFunction extends TemplateFunctionBase
{
	public function __construct(TemplatePluginArgument $argument)
	{
		parent::__construct($argument);
	}

	#region TemplateFunctionBase

	public function getFunctionName(): string
	{
		return 'pager';
	}

	protected function functionBodyImpl(): string
	{
		/** @var Pagination */
		$pagination = $this->params['data'];

		$shortcuts = $pagination->getShortcuts();
		if (empty($shortcuts)) {
			return '';
		}

		/** @var string */
		$href = $this->params['href'];
		if (Text::isNullOrWhiteSpace($href)) {
			throw new Exception('href');
		}
		$href = Code::toLiteralString($href);

		$jumpHead = Arr::getOr($this->params, 'jump-head', '<<');
		$jumpTail = Arr::getOr($this->params, 'jump-tail', '>>');
		$jumpPrev = Arr::getOr($this->params, 'jump-prev', '<');
		$jumpNext = Arr::getOr($this->params, 'jump-next', '>');

		$dom = new HtmlDocument();

		$parent = $dom->addTagElement('ul');
		$parent->addClass('pagination');

		foreach ($shortcuts as $index => $shortcut) {
			$item = $parent->addTagElement('li');
			$link = $item->addTagElement('a');

			$item->addClass($shortcut->kind->value);

			if ($shortcut->enabled) {
				$link->setAttribute('href', Text::replaceMap(
					$href,
					[
						'page_number' => (string)$shortcut->pageNumber,
					],
					'<',
					'>'
				));
			} else {
				$item->addClass('disabled');
				$link->addClass('disabled');
				$link->setAttribute('tabindex', '-1');
			}

			if ($shortcut->kind === PageShortcutKind::Long) {
				if ($index === 0) {
					$link->addText($jumpHead);
				} else {
					$link->addText($jumpTail);
				}
			} elseif ($shortcut->kind === PageShortcutKind::Short) {
				if ($index <= 1) {
					$link->addText($jumpPrev);
				} else {
					$link->addText($jumpNext);
				}
			} else {
				$link->addText((string)$shortcut->pageNumber);
				if ($shortcut->current) {
					$item->addClass('current');
					$link->addClass('current');
				}
			}
		}


		return $dom->build();
	}

	#endregion
}
