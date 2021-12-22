{extends file='default.tpl'}
{block name=TITLE}ログイン{/block}
{block name=BODY}

<form class="page-login" action="/account/login" method="post">
	<dl class="input">
		<dt>id</dt>
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
		<dd>
			<button>submit</button>
		</dd>
	</dl>
</form>

{/block}
