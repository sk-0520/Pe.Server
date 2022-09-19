{extends file='default.tpl'}
{block name='TITLE'}ユーザー登録 2/2{/block}
{block name='BODY'}

	<form class="page-account-sign-up" action="/account/signup/{$values.token}" method="post">
		<dl class="input">
			<dt>メールアドレス</dt>
			<dd>
				{input_helper key='account_signup_email' type="email" class="edit" required="true"}
			</dd>

			<dt>ログインID</dt>
			<dd>
				{input_helper key='account_signup_login_id' type="text" class="edit" required="true"}
			</dd>

			<dt>パスワード</dt>
			<dd>
				{input_helper key='account_signup_password' type="password" class="edit" required="true"}
			</dd>

			<dt>パスワード(確認用)</dt>
			<dd>
				{input_helper key='account_signup_password_confirm' type="password" class="edit" required="true"}
			</dd>

			<dt>名前</dt>
			<dd>
				{input_helper key='account_signup_name' type="text" class="edit" required="true"}
			</dd>

			<dt class="action"></dt>
			<dd class="action">
				<button>登録</button>
			</dd>
		</dl>
	</form>

{/block}
