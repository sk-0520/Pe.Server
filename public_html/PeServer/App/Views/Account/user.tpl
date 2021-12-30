{extends file='default.tpl'}
{block name=TITLE}ユーザー情報{/block}
{block name=BODY}

<dl class="page-account-user">
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
		{$values.account_user_name}
	</dd>

	<dt>email</dt>
	<dd>
		{$values.account_user_email}
	</dd>

	<dt>website</dt>
	<dd>
		{$values.account_user_website}
	</dd>

	<dt>plugin</dt>
	<dd>
		list
	</dd>

	<dt class="action">edit</dt>
	<dd class="action">
		<ul>
			<li>
				<a>register plugin</a>
			</li>
			<li>
				<a href="/account/user/edit">edit user</a>
			</li>
			<li>
				<a href="/account/user/email">change email</a>
			</li>
			<li>
				<a href="/account/user/password">change password</a>
			</li>
		</ul>
	</dd>
</dl>

{/block}
