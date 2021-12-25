{extends file='default.tpl'}
{block name=TITLE}ログイン{/block}
{block name=BODY}

<form class="page-account-login" action="/account/login" method="post">
	<dl class="input">
		<dt>login id</dt>
		<dd>
			{input_helper key='account_login_login_id' class="edit" type="text"}
		</dd>

		<dt>password</dt>
		<dd>
			{input_helper key='account_login_password' class="edit" type="text"}
		</dd>

		<dl class="action"></dl>
		<dd class="action">
			<button>submit</button>
		</dd>
	</dl>
</form>

{/block}
