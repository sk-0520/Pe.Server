{extends file='default.tpl'}
{$is_register = !isset($values.from_account_plugin_plugin_id) || empty($values.from_account_plugin_plugin_id) }
{if $is_register}
	{block name='TITLE'}プラグイン 登録{/block}
	{$action = "/account/user/plugin"}
	{$readonly = false}
{else}
	{block name='TITLE'}プラグイン 更新: {$values.account_plugin_plugin_name}{/block}
	{$action = "/account/user/plugin/{$values['from_account_plugin_plugin_id']}"}
	{$readonly = true}
{/if}
{block name='BODY'}

<form class="page-account-plugin" action="{$action}" method="post">
	{csrf}
	{if !$is_register}
		<input type="hidden" name="from_account_plugin_plugin_id" value="{$values.from_account_plugin_plugin_id}" />
	{/if}

	<dl>
		<dt>plugin id</dt>
		<dd>
			{input_helper key='account_plugin_plugin_id' type="text" class="edit" readonly="{$readonly}"}
		</dd>

		<dt>plugin name</dt>
		<dd>
			{input_helper key='account_plugin_plugin_name' type="text" class="edit" readonly="{$readonly}"}
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
