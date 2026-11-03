<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\ManagementControl;

use Override;
use PeServer\App\Models\Data\Dto\UserListItemDto;
use PeServer\App\Views\Pages\Management\ManagementPageBase;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\DataAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableCellAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableHeaderCellAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\Node\TextNode;

/**
 * @template TRootProps of RootProps
 * @extends ManagementPageBase<TRootProps>
 */
class ManagementControlUserListPage extends ManagementPageBase
{
	#[Override]
	protected function getNode(): INode
	{
		$b = $this->context->builder;
		/** @var list<UserListItemDto> $users */
		$users = $this->props->parameter->values['users'];
		$headers = [];
		foreach (['ユーザーID', '状態', '名前', 'ログインID', 'レベル', '操作'] as $label) {
			$headers[] = $b->th(new HTMLTableHeaderCellAttributes(), new TextNode($label));
		}
		$rows = [];
		foreach ($users as $user) {
			$cells = [];
			foreach ([$user->userId, $user->state, $user->name, $user->loginId, $user->level] as $index => $value) {
				$copy = in_array($index, [0, 2, 3], true);
				$cells[] = $b->td(
                    new HTMLTableCellAttributes(data: $copy ? new DataAttributes(['clipboard' => 'block']) : null),
                    $copy ? $b->code(new HtmlGlobalAttributes(), new TextNode($value)) : new TextNode($value)
                );
			}
			$cells[] = $b->td(new HTMLTableCellAttributes(className: 'mute'), new TextNode('未実装'));
			$rows[] = $b->tr(new HtmlGlobalAttributes(), new Fragment($cells));
		}
		if ($rows === []) {
			$rows[] = $b->tr(new HtmlGlobalAttributes(), $b->td(new HTMLTableCellAttributes(colSpan: 6), new TextNode('誰もおらん(こんなことない)。')));
		}
		return $this->layout('ユーザー一覧', $b->table(new HtmlGlobalAttributes(), new Fragment([
			$b->thead(new HtmlGlobalAttributes(), $b->tr(new HtmlGlobalAttributes(), new Fragment($headers))),
			$b->tbody(new HtmlGlobalAttributes(), new Fragment($rows)),
		])));
	}
}
