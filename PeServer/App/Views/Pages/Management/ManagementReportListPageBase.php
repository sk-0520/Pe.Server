<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Management;

use Override;
use PeServer\App\Models\Data\Dto\CrashReportListItemDto;
use PeServer\App\Models\Data\Dto\FeedbackListItemDto;
use PeServer\App\Models\Data\ReportStatus;
use PeServer\Core\Mvc\Pagination;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\DataAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLButtonAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableCellAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableHeaderCellAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTemplateAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Components\Csrf\Csrf;
use PeServer\Core\Mvc\Template\Node\Html\Components\Csrf\CsrfProps;
use PeServer\Core\Mvc\Template\Node\Html\Components\Pager\Pager;
use PeServer\Core\Mvc\Template\Node\Html\Components\Pager\PagerProps;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\Node\TextNode;

/**
 * @template TRootProps of RootProps
 * @extends ManagementPageBase<TRootProps>
 */
abstract class ManagementReportListPageBase extends ManagementPageBase
{
	protected const bool IS_CRASH = false;

	#[Override]
	protected function getNode(): INode
	{
		$b = $this->context->builder;
		$kind = static::IS_CRASH ? 'crash-report' : 'feedback';
		$title = static::IS_CRASH ? 'クラッシュレポート' : 'フィードバック';
		/** @var Pagination $pager */
		$pager = $this->props->parameter->values['pager'];
		/** @var list<FeedbackListItemDto>|list<CrashReportListItemDto> $items */
		$items = $this->props->parameter->values['items'];
		$nodes = [
			$b->template(
                new HTMLTemplateAttributes(id: 'pg-delete-dialog'),
				$b->div(
                    new HtmlGlobalAttributes(className: 'dialog ' . $kind . '-delete'),
					$b->p(new HtmlGlobalAttributes(), new TextNode('けす！'))
				)
			),
		];
		if ($pager->totalItemCount) {
			$nodes[] = $b->p(new HtmlGlobalAttributes(className: 'search-count-area'), new Fragment([
				$b->em(new HtmlGlobalAttributes(className: 'count'), new TextNode((string)$pager->totalItemCount)),
				$b->span(new HtmlGlobalAttributes(className: 'unit'), new TextNode('件')),
			]));
			$labels = ['sequence' => '*', 'timestamp' => '日時', 'status' => '状態', 'version' => 'バージョン'];
			if (static::IS_CRASH) {
				$labels['exception-subject'] = '例外';
			} else {
				$labels['kind'] = '種類';
				$labels['subject'] = '件名';
			}
			$labels['detail'] = '詳細';
			$labels['delete'] = '削除';
			$headers = [];
			foreach ($labels as $key => $label) {
				$headers[] = $b->th(new HTMLTableHeaderCellAttributes(className: 'column-' . $key), new TextNode($label));
			}
			$rows = [];
			foreach ($items as $item) {
				$values = [
					'sequence' => new TextNode((string)$item->sequence),
					'timestamp' => $this->timestamp($item->timestamp, 'Y-m-d\\TH:iP'),
					'status' => new TextNode(ReportStatus::toString($item->developerStatus)),
					'version' => new TextNode($item->version),
				];
				if ($item instanceof CrashReportListItemDto) {
					$values['exception-subject'] = new TextNode($item->exceptionSubject);
				} else {
					$values['kind'] = new TextNode($item->kind);
					$values['subject'] = new TextNode($item->subject);
				}
				$values['detail'] = $this->link('/management/' . $kind . '/' . $item->sequence, '詳細');
				$values['delete'] = $b->button(new HTMLButtonAttributes(className: 'pg-delete', data: new DataAttributes(['sequence' => (string)$item->sequence])), new TextNode('削除'));
				$cells = [];
				foreach ($values as $key => $value) {
					$cells[] = $b->td(new HTMLTableCellAttributes(className: 'column-' . $key), $value);
				}
				$rows[] = $b->tr(new HtmlGlobalAttributes(), new Fragment($cells));
			}
			$nodes[] = $b->table(new HtmlGlobalAttributes(className: 'search-result ' . $kind . '-list-result'), new Fragment([
				$b->thead(new HtmlGlobalAttributes(), $b->tr(new HtmlGlobalAttributes(), new Fragment($headers))),
				$b->tbody(new HtmlGlobalAttributes(), new Fragment($rows)),
			]));
			$nodes[] = new Pager(new PagerProps($pager, '/management/' . $kind . '/page/<page_number>'), $this->context);
		} else {
			$nodes[] = $b->p(new HtmlGlobalAttributes(className: 'search-not-found'), new TextNode('データなし'));
		}
		return $this->layout($title, new Fragment($nodes), '/scripts/management_' . str_replace('-', '_', $kind) . '_list.js', new Csrf(new CsrfProps('id'), $this->context));
	}
}
