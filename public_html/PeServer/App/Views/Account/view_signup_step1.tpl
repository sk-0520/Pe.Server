{extends file='default.tpl'}
{block name='TITLE'}ユーザー情報 編集{/block}
{block name='BODY'}

<form class="page-account-signup" action="/account/signup" method="post">
	<input name="account_signup_token" type="hidden" value="{$values.account_signup_token}" />
	<dl>
		<dt>email</dt>
		<dd>
			{input_helper key='account_signup_email' type="email" class="edit"}
		</dd>

		<dt>token<dt>
		<dd>
			{$values.account_signup_token}
			<br />
			{$values.value}
			<br />
			{text_image text=$values.value}
			<br />
			{input_helper key='account_signup_value' type="text" class="edit"}
		<dd>

		<dt class="action">action</dt>
		<dd class="action">
			<button>submit</button>
		</dd>
	</dl>
</form>

{/block}
