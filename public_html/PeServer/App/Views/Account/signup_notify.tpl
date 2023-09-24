{extends file='default.tpl'}
{block name='TITLE'}ユーザー登録 メール通知{/block}
{block name='BODY'}

	<p>
		<strong>アカウント登録用URLを入力されたメールアドレスに送信しました。</strong>
	</p>
	<p>
		送信されたアカウント登録用URLは一定時間で無効化されます。
	</p>

	<hr />

	{include file="template/page/email-info.tpl" email=$values.email}

{/block}
