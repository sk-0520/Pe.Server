{extends file='default.tpl'}
{$is_register = !isset($values.from_account_plugin_plugin_id) || empty($values.from_account_plugin_plugin_id) }
{if $is_register}
	{block name='TITLE'}プラグイン 登録{/block}
	{$action = "/account/user/plugin"}
	{$readonly = false}
{else}
	{block name='TITLE'}プラグイン 編集: {$values.account_plugin_plugin_name}{/block}
	{$action = "/account/user/plugin/{$values['from_account_plugin_plugin_id']}"}
	{$readonly = true}
{/if}
{block name='DEFAULT_SCRIPT'}{asset file='/scripts/plugin_edit.js'}{/block}

{block name='BODY'}

<form class="page-account-plugin" action="{$action}" method="post">
	{csrf}
	{if !$is_register}
		<input type="hidden" name="from_account_plugin_plugin_id" value="{$values.from_account_plugin_plugin_id}" />
	{/if}

	<dl class="input">
		<dt>プラグインID</dt>
		<dd>
			{input_helper key='account_plugin_plugin_id' type="text" class="edit" readonly="{$readonly}"}
		</dd>

		<dt>プラグイン内部名</dt>
		<dd>
			{input_helper key='account_plugin_plugin_name' type="text" class="edit" readonly="{$readonly}"}
		</dd>

		<dt>プラグイン表示名</dt>
		<dd>
			{input_helper key='account_plugin_display_name' type="text" class="edit"}
		</dd>

		<dt>バージョンチェックURL</dt>
		<dd>
			{input_helper key='account_plugin_check_url' type="url" class="edit"}
		</dd>

		<dt>紹介ページURL</dt>
		<dd>
			{input_helper key='account_plugin_lp_url' type="url" class="edit"}
		</dd>

		<dt>プロジェクトURL</dt>
		<dd>
			{input_helper key='account_plugin_project_url' type="url" class="edit"}
		</dd>

		<dt>説明</dt>
		<dd>
			<div class="tab">
				<input id="description_markdown_source" type="radio" name="tab_markdown" class="tab_check" checked><label for="description_markdown_source" class="tab_header">Markdown</label>
				<div class="tab_content">{input_helper key='account_plugin_description' type="textarea" class="edit markdown-editor" data-markdown-result=".markdown-browser"}</div>

				<input id="description_markdown_preview" type="radio" name="tab_markdown" class="tab_check"><label for="description_markdown_preview" class="tab_header">プレビュー</label>
				<div class="tab_content">{markdown class="markdown markdown-browser"}{$values.account_plugin_description nofilter}{/markdown}</div>
			</div>
		</dd>

		<dt class="action"></dt>
		<dd class="action">
			<button>登録</button>
		</dd>
	</dl>
</form>

{/block}

