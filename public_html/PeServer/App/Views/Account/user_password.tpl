{extends file='default.tpl'}
{block name=TITLE}ユーザー情報 編集{/block}
{block name=BODY}

<form class="page-account-password" action="/account/user/password" method="post">
	<dl>
		<dt>current password</dt>
		<dd>
			{input_helper key='account_password_current' type="text" class="edit"}
		</dd>

		<dt>password: new</dt>
		<dd>
			{input_helper key='account_password_new' type="text" class="edit"}
		</dd>

		<dt>password: confirm</dt>
		<dd>
			{input_helper key='account_password_confirm' type="text" class="edit"}
		</dd>

		<dt class="action">edit</dt>
		<dd class="action">
			<button>submit</button>
		</dd>
	</dl>
</form>

{/block}
