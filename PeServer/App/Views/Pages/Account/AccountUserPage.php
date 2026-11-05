<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Account;

use Override;
use PeServer\App\Models\Data\Dto\UserInformationDto;
use PeServer\App\Models\Domain\PluginState;
use PeServer\App\Models\Domain\UserLevel;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\DataAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLAnchorAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLiAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableCellAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableHeaderCellAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Target;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\Node\TextNode;
use PeServer\Core\Text;

/**
 * @template TRootProps of RootProps
 * @extends AccountPageBase<TRootProps>
 */
class AccountUserPage extends AccountPageBase
{
	#[Override]
	protected function getNode(): INode
	{
		$b = $this->context->builder;
		/** @var UserInformationDto $user */
		$user = $this->props->parameter->values['user'];
		/** @var array<array{plugin_id:string,plugin_name:string,display_name:string,state:string}> $plugins */
		$plugins = $this->props->parameter->values['plugins'];
		$nodes = [];
		foreach (['ユーザーID' => $user->userId, 'ログインID' => $user->loginId] as $label => $value) {
			$nodes[] = $b->dt(new HtmlGlobalAttributes(), new TextNode($label));
			$nodes[] = $b->dd(new HtmlGlobalAttributes(), $b->code(
				new HtmlGlobalAttributes(data: new DataAttributes(['role' => 'value', 'clipboard' => 'inline'])),
				new TextNode($value)
			));
		}
		foreach (['権限' => UserLevel::toString($user->level), '名前' => $user->name] as $label => $value) {
			$nodes[] = $b->dt(new HtmlGlobalAttributes(), new TextNode($label));
			$nodes[] = $b->dd(new HtmlGlobalAttributes(data: new DataAttributes(['role' => 'value'])), new TextNode($value));
		}
		$nodes[] = $b->dt(new HtmlGlobalAttributes(), new TextNode('Webサイト'));
		$nodes[] = $b->dd(
            new HtmlGlobalAttributes(data: new DataAttributes(['role' => 'value'])),
			Text::isNullOrWhiteSpace($user->website)
				? $b->span(new HtmlGlobalAttributes(className: 'mute'), new TextNode('未登録'))
            : $b->a(new HTMLAnchorAttributes(href: $user->website, target: Target::Blank, data: new DataAttributes(['role' => 'value'])), new TextNode($user->website))
        );
		$rows = [];
		foreach ($plugins as $plugin) {
			$state = match ($plugin['state']) {
				PluginState::ENABLED => '有効',
				PluginState::CHECK_FAILED => '不明',
				PluginState::RESERVED => '予約',
				PluginState::DISABLED => '無効',
				default => 'あかん',
			};
			$rows[] = $b->tr(new HtmlGlobalAttributes(), new Fragment([
				$b->th(new HTMLTableHeaderCellAttributes(), new TextNode($state)),
				$b->td(new HTMLTableCellAttributes(), $b->a(
					new HTMLAnchorAttributes(href: '/account/user/plugin/' . $plugin['plugin_id'], title: $plugin['display_name']),
                    new TextNode($plugin['plugin_name'])
                )),
				$b->td(
                    new HTMLTableCellAttributes(data: new DataAttributes(['clipboard' => 'data', 'clipboard-value' => $plugin['plugin_id']])),
                    $b->code(new HtmlGlobalAttributes(), new TextNode($plugin['plugin_id']))
                ),
			]));
		}
		$nodes[] = $b->dt(new HtmlGlobalAttributes(), new TextNode('プラグイン'));
		$nodes[] = $b->dd(
            new HtmlGlobalAttributes(data: new DataAttributes(['role' => 'value'])),
			empty($plugins)
				? $b->span(new HtmlGlobalAttributes(className: 'mute'), new TextNode('未登録'))
				: $b->table(new HtmlGlobalAttributes(), new Fragment([
					$b->thead(new HtmlGlobalAttributes(), $b->tr(new HtmlGlobalAttributes(), new Fragment([
						$b->th(new HTMLTableHeaderCellAttributes(), new TextNode('状態')),
						$b->th(new HTMLTableHeaderCellAttributes(), new TextNode('プラグイン名')),
						$b->th(new HTMLTableHeaderCellAttributes(), new TextNode('プラグインID')),
					]))),
					$b->tbody(new HtmlGlobalAttributes(), new Fragment($rows)),
			]))
        );
		$links = [
			'/account/user/edit' => 'ユーザー編集',
			'/account/user/api' => 'API設定',
			'/account/user/email' => 'メールアドレス変更',
			'/account/user/password' => 'パスワード変更',
		];
		$items = [
			$b->li(new HTMLLiAttributes(), new Fragment([
				$b->a(new HTMLAnchorAttributes(href: '/account/user/plugin'), new TextNode('プラグイン登録')),
				$b->ul(new HtmlGlobalAttributes(), $b->li(
                    new HTMLLiAttributes(),
                    $b->a(new HTMLAnchorAttributes(href: '/account/user/plugin/reserve'), new TextNode('予約'))
                )),
			])),
		];
		foreach ($links as $href => $label) {
			$items[] = $b->li(new HTMLLiAttributes(), $b->a(new HTMLAnchorAttributes(href: $href), new TextNode($label)));
		}
		$items[] = $b->li(new HTMLLiAttributes(), new Fragment([
			$b->a(new HTMLAnchorAttributes(href: '/account/user/audit-logs'), new TextNode('監査ログ')),
			new TextNode(' ('),
			$b->a(new HTMLAnchorAttributes(href: '/account/user/audit-logs/download'), new TextNode('DL')),
			new TextNode(')'),
		]));
		$nodes[] = $b->dt(new HtmlGlobalAttributes(className: 'action'), new TextNode('各種操作'));
		$nodes[] = $b->dd(new HtmlGlobalAttributes(className: 'action'), $b->ul(new HtmlGlobalAttributes(), new Fragment($items)));
		return $this->layout('ユーザー情報', $b->dl(new HtmlGlobalAttributes(className: 'page-account-user'), new Fragment($nodes)));
	}
}
