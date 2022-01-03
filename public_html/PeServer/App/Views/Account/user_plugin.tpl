{extends file='default.tpl'}
{block name='TITLE'}ユーザー情報 編集{/block}
{block name='BODY'}

{$is_register = !isset($values.from_account_plugin_plugin_id) || empty($values.from_account_plugin_plugin_id) }

{if $is_register}
	{$action = "/account/user/plugin"}
{else}
	{$action = "/account/user/plugin/{$values['from_account_plugin_plugin_id']}"}
{/if}

<form class="page-account-plugin" action="{$action}" method="post">
	{csrf}
	{if !$is_register}
		<input type="hidden" name="from_account_plugin_plugin_id" value="{$values.from_account_plugin_plugin_id}" />
	{/if}

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
			{input_helper key='account_plugin_description' type="textarea" class="edit"}
		</dd>

		<dt class="action">edit</dt>
		<dd class="action">
			<button>submit</button>
		</dd>
	</dl>
</form>

{/block}
