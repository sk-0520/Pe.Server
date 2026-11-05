<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Management;

use Override;
use PeServer\Core\Mvc\Template\Node\Fragment;
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
class ManagementIndexPage extends ManagementPageBase
{
	/** @param list<INode> $items */
	private function listing(array $items): INode
	{
		$b = $this->context->builder;
		return $b->ul(new HtmlGlobalAttributes(), new Fragment(array_map(
			fn(INode $item) => $b->li(new HTMLLiAttributes(), $item),
			$items
		)));
	}

	/** @param list<INode> $items */
	private function group(string $label, array $items): INode
	{
		return new Fragment([new TextNode($label), $this->listing($items)]);
	}

	#[Override]
	protected function getNode(): INode
	{
		$b = $this->context->builder;
		$maintenance = [];
		foreach (
            [
			'backup' => 'バックアップ',
			'delete-old-data' => '不要データ削除処理',
			'vacuum-access-log' => 'アクセスログ整理',
			'cache-rebuild' => 'キャッシュ再構築',
			'clear-deploy-progress' => 'デプロイ進捗ファイル破棄',
            ] as $path => $label
		) {
			$maintenance[] = $this->form('/management/' . $path, $b->button(new HTMLButtonAttributes(className: 'link'), new TextNode($label)));
		}
		$maintenance[] = $this->group('管理', [
			$this->link('/management/control/user', 'ユーザー一覧'),
			$this->link('/management/control/backup', 'バックアップ一覧'),
		]);
		$dbLinks = [
			$this->link('/management/database-maintenance/default', 'メインDBメンテナンス'),
			new TextNode(' ('),
			$this->link('/management/database-download/default', 'DL'),
			new TextNode(')'),
		];
		/** @var array<array{target:string,display:string}> $databases */
		$databases = $this->props->parameter->values['databases'];
		foreach ($databases as $database) {
			$dbLinks[] = $this->listing([new Fragment([
				$this->link('/management/database-maintenance/' . $database['target'], $database['display'] . 'DBメンテナンス'),
				new TextNode(' ('),
				$this->link('/management/database-download/' . $database['target'], 'DL'),
				new TextNode(')'),
			])]);
		}
		return $this->layout('管理', $this->listing([
			$this->link('/management/log', 'ログ'),
			$this->group('Pe', [
				$this->link('/management/feedback', 'フィードバック'),
				$this->link('/management/crash-report', 'クラッシュレポート'),
				$this->link('/management/version', 'バージョン設定'),
			]),
			$this->group('プラグイン', [
				$this->link('/management/default-plugin', '標準プラグイン登録'),
				$this->link('/management/plugin-category', 'プラグインカテゴリ'),
			]),
			$this->group('設定', [
				$this->link('/management/environment', '環境情報'),
				new Fragment([$this->link('/management/configuration', '現在設定'), $this->listing([$this->link('/management/configuration/edit', '編集')])]),
				$this->group('保守', $maintenance),
			]),
			$this->group('実行', [
				new Fragment($dbLinks),
				$this->link('/management/php-evaluate', 'PHP実行'),
				$this->link('/management/mail-send', 'メール送信'),
			]),
			$this->link('/management/markdown', 'Markdown'),
		]));
	}
}
