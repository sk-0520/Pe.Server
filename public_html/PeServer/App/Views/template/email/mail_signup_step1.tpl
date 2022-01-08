{extends file='email.tpl'}
{block name='BODY'}

<p>
	ユーザー登録用URLを発行しました。
</p>

<div class="main" style="text-align:center">
	<a href="{$values.url}">本登録ページへ</a>
</div>

URL: <code>{$values.url}</code>

{/block}
