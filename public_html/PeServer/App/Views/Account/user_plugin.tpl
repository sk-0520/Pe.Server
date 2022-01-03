{extends file='default.tpl'}
{block name='TITLE'}ユーザー情報 編集{/block}
{block name='BODY'}

<form class="page-account-plugin" action="/account/user/plugin" method="post">
	{csrf}

	<dl>
		<dt>plugin id</dt>
		<dd>
			{input_helper key='account_plugin_plugin_id' type="text" class="edit"}
		</dd>

		<dt>plugin name</dt>
		<dd>
			{input_helper key='account_plugin_plugin_name' type="text" class="edit"}
		</dd>

		<dt>display name</dt>
		<dd>
			{input_helper key='account_plugin_display_name' type="text" class="edit"}
		</dd>

		<dt>check url</dt>
		<dd>
			{input_helper key='account_plugin_check_url' type="text" class="edit"}
		</dd>

		<dt>landing page url</dt>
		<dd>
			{input_helper key='account_plugin_lp_url' type="text" class="edit"}
		</dd>

		<dt>project url</dt>
		<dd>
			{input_helper key='account_plugin_project_url' type="text" class="edit"}
		</dd>

		<dt>description</dt>
		<dd>
			{input_helper key='account_plugin_description' type="text" class="edit"}
		</dd>

		<dt class="action">edit</dt>
		<dd class="action">
			<button>submit</button>
		</dd>
	</dl>
</form>

{/block}
