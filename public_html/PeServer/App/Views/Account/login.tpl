{extends file='default.tpl'}
{block name=TITLE}ログイン{/block}
{block name=BODY}

<form class="page-account-login" action="/account/login" method="post">
	<dl class="input">
		<dt>login id</dt>
		<dd>
			{input_helper key='account_login_login_id' type="text" class="edit"}
		</dd>

		<dt>password</dt>
		<dd>
			{input_helper key='account_login_password' type="password" class="edit"}
		</dd>

		<dl class="action"></dl>
		<dd class="action">
			<button>submit</button>
		</dd>
	</dl>
</form>

{/block}
