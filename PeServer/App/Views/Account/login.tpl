{extends file='default.tpl'}
{block name='TITLE'}ログイン{/block}
{block name='BODY'}

	<form class="page-account-login" action="/account/login" method="post">
		{csrf}

		<dl class="input">
			<dt>ログインID</dt>
			<dd>
				{input_helper key='account_login_login_id' type="text" class="edit" autofocus="true" required="true"}
			</dd>

			<dt>パスワード</dt>
			<dd>
				{input_helper key='account_login_password' type="password" class="edit" required="true"}
			</dd>

			<dt class="action"></dt>
			<dd class="action">
				<button>ログイン</button>
			</dd>

			<dd class="password-reminder">
				<p>
				<a href="/password/reminder">パスワードを忘れた場合はこちら</a>
				</p>
			</dd>
		</dl>
	</form>

	{if $environment->isDevelopment() }
		<form action="/account/login" method="post" style="text-align: center; margin-top: 4em">
			{csrf}
			<input type="hidden" name="account_login_login_id" value="root" />
			<input type="hidden" name="account_login_password" value="root" />
			<button class="link" data-dialog="disabled">開発ログイン(管理)</button>
		</form>
		<form action="/account/login" method="post" style="text-align: center;">
			{csrf}
			<input type="hidden" name="account_login_login_id" value="user" />
			<input type="hidden" name="account_login_password" value="user" />
			<button class="link" data-dialog="disabled">開発ログイン(通常)</button>
		</form>
	{/if}

{/block}
