{extends file='default.tpl'}
{block name='TITLE'}ユーザー情報{/block}
{block name='BODY'}

<dl class="page-account-user">
	<dt>ユーザーID</dt>
	<dd>
		<code data-clipboard="inline">{$values.account_user_id}</code>
	</dd>

	<dt>ログインID</dt>
	<dd>
		{$values.account_user_login_id}
	</dd>

	<dt>権限</dt>
	<dd>
		{PeServer\App\Models\Domain\UserLevel::toString($values.account_user_level)}
	</dd>

	<dt>名前</dt>
	<dd>
		{$values.account_user_name}
	</dd>

	<dt>Webサイト</dt>
		<dd>
			{if PeServer\Core\StringUtility::isNullOrWhiteSpace($values.account_user_website)}
				<span class="mute">未登録</span>
			{else}
				<a href="{$values.account_user_website}" target="_blank">{$values.account_user_website}</a>
			{/if}
		</dd>

	<dt>プラグイン</dt>
	<dd>
		{if empty($values.plugins)}
			<span class="mute">未登録</span>
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
				<a href="/account/user/email">メールアドレス変更</a>
			</li>
			<li>
				<a href="/account/user/password">パスワード変更</a>
			</li>
		</ul>
	</dd>
</dl>

{/block}
