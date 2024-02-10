{extends file='email.tpl'}
{block name='BODY'}

<p>
	{$values.name} さんのユーザー登録が完了しました。
</p>

<div class="main" style="text-align:center">
	ログインID: <code>{$values.login_id}</code>
</div>

{/block}
