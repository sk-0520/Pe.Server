{extends file='email.tpl'}
{block name='BODY'}

<p>
	ログインID: <code>{$values.login_id}</code> 宛のパスワード再発行通知です。
</p>

<div class="main" style="text-align:center">
	<a href="{$values.url}">再発行ページへ</a>
</div>

URL: <code>{$values.url}</code>

{/block}
