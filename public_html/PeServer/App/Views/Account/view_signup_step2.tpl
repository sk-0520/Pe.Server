{extends file='default.tpl'}
{block name='TITLE'}ユーザー情報 編集{/block}
{block name='BODY'}

<form class="page-account-sign-up" action="/account/signup/{$values.token}" method="post">
	<dl class="input">
		<dt>email</dt>
		<dd>
			{input_helper key='account_signup_email' type="email" class="edit"}
		</dd>

		<dt>login id</dt>
		<dd>
			{input_helper key='account_signup_login_id' type="text" class="edit"}
		</dd>

		<dt>password</dt>
		<dd>
			{input_helper key='account_signup_password' type="password" class="edit"}
		</dd>

		<dt>password confirm</dt>
		<dd>
			{input_helper key='account_signup_password_confirm' type="password" class="edit"}
		</dd>

		<dt>name</dt>
		<dd>
			{input_helper key='account_signup_name' type="text" class="edit"}
		</dd>

		<dt class="action">action</dt>
		<dd class="action">
			<button>submit</button>
		</dd>
	</dl>
</form>

{/block}
