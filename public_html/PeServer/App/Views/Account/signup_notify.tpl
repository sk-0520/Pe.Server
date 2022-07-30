{extends file='default.tpl'}
{block name='TITLE'}ユーザー登録 メール通知{/block}
{block name='BODY'}

<p>
	<strong>アカウント登録用URLを入力されたメールアドレスに送信しました。</strong>
</p>

<h2>注意</h2>
<ul>
	<li>
		受信拒否をしている場合、<code data-clipboard="inline">{$values.email_address}</code>を許可してください。
		<ul>
			<li>その他通知メールを送信する可能性もあるのでドメイン<code data-clipboard="inline">{$values.email_domain}</code>を許可してもらえるとうれしいです。</li>
		</ul>
	</li>
	<li>送信されたアカウント登録用URLは一定時間で無効化されます。</li>
</ul>

{/block}
