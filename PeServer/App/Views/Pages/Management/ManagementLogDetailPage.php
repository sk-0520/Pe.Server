<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Management;

use Override;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\DataAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLButtonAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLiAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\Node\TextNode;

/**
 * @template TRootProps of RootProps
 * @extends ManagementPageBase<TRootProps>
 */
class ManagementLogDetailPage extends ManagementPageBase
{
	#[Override]
	protected function getNode(): INode
	{
		$b = $this->context->builder;
		$v = $this->props->parameter->values;
		return $this->layout('ログ詳細: ' . $v['log_name'], $b->dl(new HtmlGlobalAttributes(), new Fragment([
			$b->dt(new HtmlGlobalAttributes(), $b->ul(new HtmlGlobalAttributes(className: 'inline'), new Fragment([
				$b->li(new HTMLLiAttributes(), $this->form(null, $b->button(new HTMLButtonAttributes(), new TextNode('ダウンロード')))),
				$b->li(new HTMLLiAttributes(), $b->code(new HtmlGlobalAttributes(data: new DataAttributes(['clipboard' => 'inline'])), new TextNode($v['log_file']))),
			]))),
			$b->dd(new HtmlGlobalAttributes(), $b->pre(new HtmlGlobalAttributes(data: new DataAttributes(['clipboard' => 'block'])), new TextNode($v['log_value']))),
		])));
	}
}
