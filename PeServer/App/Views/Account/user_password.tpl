{extends file='default.tpl'}
{block name='TITLE'}パスワード変更{/block}
{block name='BODY'}

	<form class="page-account-password" action="/account/user/password" method="post">
		{csrf}

		<dl class="input">
			<dt>現在パスワード</dt>
			<dd>
				{input_helper key='account_password_current' type="password" class="edit" required="true"}
			</dd>

			<dt>新しいパスワード</dt>
			<dd>
				{input_helper key='account_password_new' type="password" class="edit" required="true"}
			</dd>

			<dt>新しいパスワード(確認用)</dt>
			<dd>
				{input_helper key='account_password_confirm' type="password" class="edit" required="true"}
			</dd>

			<dt class="action"></dt>
			<dd class="action">
				<button>登録</button>
			</dd>
		</dl>
	</form>

{/block}
