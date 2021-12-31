{extends file='email.tpl'}
{block name='BODY'}

<p>
	{$values.name} さん宛のメールアドレス変更完了通知になります。<br>
	本メールをもってこのメールアドレスにメール配信されることはありません。
</p>

<p style="color:#ff0000;font-weight:bold">
	このメールアドレス変更が自分の意志で行われていない場合問い合わせをお願いします。
</p>
<p>
	問い合わせ: <a href="{$values.app.contact_url}">{$values.app.contact_url}</a>
</p>

{/block}
