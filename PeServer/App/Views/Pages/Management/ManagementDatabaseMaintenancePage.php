<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Management;

use Override;
use PeServer\Core\Database\DatabaseTableResult;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\DataAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLBrAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLButtonAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLDetailsAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLiAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableCellAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableHeaderCellAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputHiddenAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\ButtonType;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelper;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelperProps;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelperType;
use PeServer\Core\Mvc\Template\Node\Html\JavaScriptSourceNode;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\Node\TextNode;
use PeServer\Core\Text;

/**
 * @template TRootProps of RootProps
 * @extends ManagementPageBase<TRootProps>
 */
class ManagementDatabaseMaintenancePage extends ManagementPageBase
{
	#[Override]
	protected function getNode(): INode
	{
		$b = $this->context->builder;
		$p = $this->props->parameter;
		/** @var string $database */
		$database = $p->values['database'];
		/** @var array<array{name:string,columns:array<mixed>}> $tables */
		$tables = $p->values['tables'];
		$tableButtons = [];
		foreach ($tables as $table) {
			$tableButtons[] = $b->li(new HTMLLiAttributes(className: 'inline'), $b->button(new HTMLButtonAttributes(
				className: 'pg-table',
                type: ButtonType::Button,
				data: new DataAttributes(['table' => $table['name'], 'columns' => json_encode($table['columns'], JSON_THROW_ON_ERROR)]),
			), new TextNode($table['name'])));
		}
		$nodes = [
			$b->h2(new HtmlGlobalAttributes(), new TextNode('メンテ')),
			$b->ul(new HtmlGlobalAttributes(), $b->li(new HTMLLiAttributes(), $this->link('/management/database-download/' . $database, 'ダウンロード'))),
			$b->h2(new HtmlGlobalAttributes(), new TextNode('入力')),
			$this->form('/management/database-maintenance/' . $database, new Fragment([
				$b->inputHidden(new HTMLInputHiddenAttributes(id: 'tables_open', name: 'tables_open', value: $p->values['tables_open'])),
				$b->dl(new HtmlGlobalAttributes(className: 'input'), new Fragment([
					$this->field('SQL', new Fragment([
						new InputHelper(new InputHelperProps($p, 'database_maintenance_statement', InputHelperType::Textarea, 'edit', true), $this->context),
						$b->details(new HTMLDetailsAttributes(id: 'tables', open: $p->values['tables_open'] === 'true'), new Fragment([
							$b->summary(new HtmlGlobalAttributes(), new TextNode('TABLE/VIEW')),
							$b->ul(new HtmlGlobalAttributes(className: 'inline'), new Fragment($tableButtons)),
						])),
					])),
					$this->submit('submit', '実行'),
				])),
			]), 'page-setting-database-maintenance'),
		];
		if ($p->values['executed']) {
			$result = $p->values['result'];
			if ($result instanceof DatabaseTableResult) {
				$headers = [$b->th(new HTMLTableHeaderCellAttributes(), new TextNode('-'))];
				foreach ($result->columns as $column) {
					$headers[] = $b->th(new HTMLTableHeaderCellAttributes(data: new DataAttributes(['clipboard' => 'block'])), new TextNode($column->name));
				}
				$rows = [];
				foreach ($result->rows as $index => $row) {
					$cells = [$b->th(new HTMLTableHeaderCellAttributes(), new TextNode((string)($index + 1)))];
					foreach ($row as $cell) {
						$lines = [];
						foreach (Text::splitLines(Text::toString($cell)) as $line) {
							if ($lines !== []) {
								$lines[] = $b->br(new HTMLBrAttributes());
							}
							$lines[] = new TextNode($line);
						}
						$cells[] = $b->td(new HTMLTableCellAttributes(data: new DataAttributes(['clipboard' => 'block'])), new Fragment($lines));
					}
					$rows[] = $b->tr(new HtmlGlobalAttributes(), new Fragment($cells));
				}
				$nodes[] = $b->h2(new HtmlGlobalAttributes(), new TextNode('結果: ' . $result->getRowsCount()));
				$nodes[] = $b->table(new HtmlGlobalAttributes(), new Fragment([
					$b->thead(new HtmlGlobalAttributes(), $b->tr(new HtmlGlobalAttributes(), new Fragment($headers))),
					$b->tbody(new HtmlGlobalAttributes(), new Fragment($rows)),
				]));
				$nodes[] = $b->h3(new HtmlGlobalAttributes(), new TextNode('影響件数'));
				$nodes[] = $b->pre(new HtmlGlobalAttributes(), new TextNode((string)$result->getResultCount()));
			} else {
				$nodes[] = $b->h2(new HtmlGlobalAttributes(), new TextNode('結果: エラー'));
				$nodes[] = $b->pre(new HtmlGlobalAttributes(data: new DataAttributes(['clipboard' => 'block'])), new TextNode(Text::toString($result)));
			}
		}
		return $this->layout(
            'DBメンテナンス(' . $database . ')',
            new Fragment($nodes),
            '/scripts/management_database_maintenance.js',
			scriptCode: new JavaScriptSourceNode(<<<'JS'
				window.addEventListener('load', () => {
					const tablesOpenElement = document.getElementById('tables_open');
					const tablesElement = document.getElementById('tables');
					tablesElement.addEventListener("toggle", ev => {
						tablesOpenElement.value = tablesElement.open;
					});
				});
				JS)
		);
	}
}
