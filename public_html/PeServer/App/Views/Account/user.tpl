{extends file='default.tpl'}
{block name='TITLE'}ユーザー情報{/block}
{block name='BODY'}

	<dl class="page-account-user">
		<dt>ユーザーID</dt>
		<dd>
			<code data-role="value" data-clipboard="inline">{$values.user->userId}</code>
		</dd>

		<dt>ログインID</dt>
		<dd>
			<code data-role="value" data-clipboard="inline">{$values.user->loginId}</code>
		</dd>

		<dt>権限</dt>
		<dd>
			<span data-role="value">{PeServer\App\Models\Domain\UserLevel::toString($values.user->level)}</span>
		</dd>

		<dt>名前</dt>
		<dd data-role="value">
			<span data-role="value">{$values.user->name}</span>
		</dd>

		<dt>Webサイト</dt>
		<dd>
			{if PeServer\Core\Text::isNullOrWhiteSpace($values.user->website)}
				<span class="mute" data-role="value">未登録</span>
			{else}
				<a href="{$values.user->website}" target="_blank" data-role="value">{$values.user->website}</a>
			{/if}
		</dd>

		<dt>プラグイン</dt>
		<dd>
			{if empty($values.plugins)}
				<span class="mute" data-role="value">未登録</span>
			{else}
				<ul>
					{foreach from=$values.plugins item=item key=key name=name}
						<li data-index={$key}>
							<a href="/account/user/plugin/{$item.plugin_id}" title="{$item.display_name}">
								{$item.plugin_name}
							</a>
						</li>
					{/foreach}
				</ul>
			{/if}
		</dd>

		<dt class="action">各種操作</dt>
		<dd class="action">
			<ul>
				<li>
					<a href="/account/user/plugin">プラグイン登録</a>
				</li>
				<li>
					<a href="/account/user/edit">ユーザー編集</a>
				</li>
				<li>
					<a href="/account/user/api">API設定</a>
				</li>
				<li>
					<a href="/account/user/email">メールアドレス変更</a>
				</li>
				<li>
					<a href="/account/user/password">パスワード変更</a>
				</li>
				<li>
					<a href="/account/user/audit-logs">監査ログ</a> (<a href="/account/user/audit-logs/download">DL</a>)
				</li>
			</ul>
		</dd>
	</dl>

{/block}
