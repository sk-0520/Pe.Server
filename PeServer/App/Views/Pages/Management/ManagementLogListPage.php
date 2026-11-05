<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Management;

use Override;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\DataAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLAnchorAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLButtonAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLiAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLOlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Components\Csrf\Csrf;
use PeServer\Core\Mvc\Template\Node\Html\Components\Csrf\CsrfProps;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\Node\TextNode;

/**
 * @template TRootProps of RootProps
 * @extends ManagementPageBase<TRootProps>
 */
class ManagementLogListPage extends ManagementPageBase
{
	#[Override]
	protected function getNode(): INode
	{
		$b = $this->context->builder;
		/** @var list<array{name:string,size:int,human_size:string}> $files */
		$files = $this->props->parameter->values['log_files'];
		$items = [];
		foreach ($files as $file) {
			$items[] = $b->li(new HTMLLiAttributes(), new Fragment([
				$b->button(new HTMLButtonAttributes(className: 'pg-delete', title: '削除', data: new DataAttributes(['name' => $file['name']])), new TextNode('🗑️')),
				new TextNode(' '),
				$b->a(new HTMLAnchorAttributes(className: 'monospace', href: '/management/log/' . $file['name']), new TextNode($file['name'])),
				new TextNode(' '),
				$b->span(new HtmlGlobalAttributes(title: $file['size'] . ' byte'), new TextNode($file['human_size'])),
			]));
		}
		if ($items === []) {
			$items[] = $b->li(new HTMLLiAttributes(), new TextNode('ファイルないよ'));
		}
		return $this->layout('ログ一覧', $b->dl(new HtmlGlobalAttributes(), new Fragment([
			$this->field('ディレクトリ', $b->code(new HtmlGlobalAttributes(data: new DataAttributes(['clipboard' => 'inline'])), new TextNode($this->props->parameter->values['directory']))),
			$this->field('ファイル', $b->ol(new HTMLOlAttributes(), new Fragment($items))),
		])), '/scripts/management_log_list.js', new Csrf(new CsrfProps('id'), $this->context));
	}
}
