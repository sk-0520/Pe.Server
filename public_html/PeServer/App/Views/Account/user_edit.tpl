{extends file='default.tpl'}
{block name='TITLE'}ユーザー情報 編集{/block}
{block name='BODY'}

<form class="page-account-user" action="/account/user/edit" method="post">
	{csrf}

	<dl class="input">
		<dt>user name</dt>
		<dd>
			{input_helper key='account_edit_name' type="text" class="edit"}
		</dd>

		<dt>website</dt>
		<dd>
			{input_helper key='account_edit_website' type="url" class="edit"}
		</dd>

		<dt>website</dt>
		<dd>
			{input_helper key='account_edit_description' type="textarea" class="edit"}
		</dd>

		<dt class="action">edit</dt>
		<dd class="action">
			<button>submit</button>
		</dd>
	</dl>
</form>

{/block}
