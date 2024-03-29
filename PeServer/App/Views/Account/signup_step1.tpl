{extends file='default.tpl'}
{block name='TITLE'}ユーザー登録 1/2{/block}
{block name='BODY'}

	<form class="page-account-sign-up" action="/account/signup" method="post">
		{csrf}

		<input name="account_signup_token" type="hidden" value="{$values.account_signup_token}" />
		<dl class="input">
			<dt>メールアドレス</dt>
			<dd>
				{input_helper key='account_signup_email' type="email" class="edit" required="true"}
			</dd>

			<dt>認証トークン</dt>
			<dd>
				トークン: {bot_text_image alt="トークン" text=$values.value width=56 height=28 font-size="12" background-color="#eeeeeeff" foreground-color="#0c0c0c" obfuscate-level=0 class="token"}
				<br />
				上記トークンを入力してください。
				<br />
				{input_helper key='account_signup_value' type="text" class="edit" required="true"}
			<dd>

			<dt class="action">確認</dt>
			<dd class="action">
				<button>メール送信</button>
			</dd>
			<dd>
				{include file="template/page/email-info.tpl" email=$values.email}
			</dd>
		</dl>
	</form>

{/block}
