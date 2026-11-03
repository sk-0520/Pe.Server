<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Account;

use Override;
use PeServer\Core\Database\DatabaseTableResult;
use PeServer\Core\Mvc\Pagination;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\DataAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableCellAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableHeaderCellAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Components\Code\Code;
use PeServer\Core\Mvc\Template\Node\Html\Components\Code\CodeProps;
use PeServer\Core\Mvc\Template\Node\Html\Components\Pager\Pager;
use PeServer\Core\Mvc\Template\Node\Html\Components\Pager\PagerProps;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\Node\TextNode;

/**
 * @template TRootProps of RootProps
 * @extends AccountPageBase<TRootProps>
 */
class AccountUserAuditLogsPage extends AccountPageBase
{
	#[Override]
	protected function getNode(): INode
	{
		$b = $this->context->builder;
		/** @var DatabaseTableResult<array{timestamp:string,event:string,info:string,ip_address:string,user_agent:string}> $items */
		$items = $this->props->parameter->values['items'];
		/** @var Pagination $pager */
		$pager = $this->props->parameter->values['pager'];
		$headers = [];
		foreach (['*', 'TIMESTAMP', 'EVENT', 'INFO', 'IP ADDR', 'UA'] as $label) {
			$headers[] = $b->th(new HTMLTableHeaderCellAttributes(), new TextNode($label));
		}
		$rows = [];
		foreach ($items->rows as $key => $item) {
			$rows[] = $b->tr(new HtmlGlobalAttributes(), new Fragment([
				$b->td(new HTMLTableCellAttributes(), new TextNode((string)$key)),
				$b->td(new HTMLTableCellAttributes(), new TextNode($item['timestamp'] . 'Z')),
				$b->td(new HTMLTableCellAttributes(), new TextNode($item['event'])),
				$b->td(
                    new HTMLTableCellAttributes(data: new DataAttributes(['clipboard' => 'block'])),
                    new Code(new CodeProps($item['info'], 'json'), $this->context)
                ),
				$b->td(new HTMLTableCellAttributes(), new TextNode($item['ip_address'])),
				$b->td(new HTMLTableCellAttributes(), new TextNode($item['user_agent'])),
			]));
		}
		return $this->layout('監査ログ', new Fragment([
			$b->table(new HtmlGlobalAttributes(), new Fragment([
				$b->thead(new HtmlGlobalAttributes(), $b->tr(new HtmlGlobalAttributes(), new Fragment($headers))),
				$b->tbody(new HtmlGlobalAttributes(), new Fragment($rows)),
			])),
			new Pager(new PagerProps($pager, '/account/user/audit-logs/page/<page_number>'), $this->context),
		]));
	}
}
