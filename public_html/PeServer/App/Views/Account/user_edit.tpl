{extends file='default.tpl'}
{block name=TITLE}ユーザー情報 編集{/block}
{block name=BODY}

<form class="page-account-user" action="/account/user/edit" method="post">
	<dl>
		<dt>user id</dt>
		<dd>
			{$values.account_user_id}
		</dd>

		<dt>login id</dt>
		<dd>
			{$values.account_user_login_id}
		</dd>

		<dt>level</dt>
		<dd>
			{$values.account_user_level}
		</dd>

		<dt>user name</dt>
		<dd>
			{input_helper key='account_user_name' type="text" class="edit"}
		</dd>

		<dt>email</dt>
		<dd>
			{input_helper key='account_edit_email' type="text" class="edit"}
		</dd>

		<dt>website</dt>
		<dd>
			{input_helper key='account_edit_website' type="text" class="edit"}
		</dd>

		<dt class="action">edit</dt>
		<dd class="action">
			<button>submit</button>
		</dd>
	</dl>
</form>

{/block}
