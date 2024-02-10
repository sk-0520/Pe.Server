{extends file='email.tpl'}
{block name='BODY'}

<p>
	{$values.name} さん宛のメールアドレス変更確認になります。<br>
	以下のトークンを使用してメールアドレス変更を完了してください。
</p>

<div class="main" style="text-align:center">
	<code>{$values.token}</code>
</div>

<p>
	トークン期限が切れた後でもトークン発行は何度でも行えます。
</p>

<p>
	本メールはユーザー起因で送信されています。<br>
	心当たりがない場合は問い合わせまでご連絡ください。
</p>
<p>
	問い合わせ: <a href="{$values.app.contact_url}">{$values.app.contact_url}</a>
</p>

{/block}
