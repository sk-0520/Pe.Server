{extends file='default.tpl'}
{block name=TITLE}ログイン{/block}
{block name=BODY}

<form class="page-account-login" action="/account/login" method="post">
	<dl class="input">
		<dt>login id</dt>
		<dd>
			<input class="edit" name="account_login_login_id" type="text" value="" />
			{show_error_messages key='account_login_login_id'}
		</dd>

		<dt>password</dt>
		<dd>
			<input class="edit" name="account_login_password" type="password" value="" />
			{show_error_messages key='account_login_password'}
		</dd>

		<dl class="action"></dl>
		<dd class="action">
			<button>submit</button>
		</dd>
	</dl>
</form>

{/block}
