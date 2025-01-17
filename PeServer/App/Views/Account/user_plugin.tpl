{extends file='default.tpl'}
{$isRegister = !isset($values.from_account_plugin_plugin_id) || empty($values.from_account_plugin_plugin_id) }
{if $isRegister}
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
		{if !$isRegister}
			<input type="hidden" name="from_account_plugin_plugin_id" value="{$values.from_account_plugin_plugin_id}" />
		{/if}

		<dl class="input">
			<dt>
				プラグインID
				{if $isRegister}
					<ul class="helper">
						<li><button id="pg-plugin-id-auto-generate" class="action sub" type="button">自動生成</button></li>
					</ul>
				{/if}
			</dt>
			<dd>
				{input_helper id='pg-plugin-id' key='account_plugin_plugin_id' type="text" class="edit" readonly="{$readonly}"}
			</dd>

			<dt>プラグイン内部名</dt>
			<dd>
				{input_helper key='account_plugin_plugin_name' type="text" class="edit" readonly="{$readonly}"}
			</dd>

			<dt>プラグイン表示名</dt>
			<dd>
				{input_helper key='account_plugin_display_name' type="text" class="edit"}
			</dd>

			<dt>状態</dt>
			<dd>
				<ul class="inline">
					{foreach from=$values.plugin_state_items item=item key=key}
						<li>
							<label>
								<input
									type="radio"
									name="account_plugin_state"
									value="{$item}"
									{if !$item|in_array:$values.plugin_state_editable_items}
										disabled
									{/if}
									{if $item == $values.account_plugin_state}
										checked
									{/if}
								/>
								{PeServer\App\Models\Domain\PluginState::toString($item)}
							</label>
						</li>
					{/foreach}
				</ul>
				{show_error_messages key='account_plugin_state'}
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

			<dt>カテゴリー</dt>
			<dd>
				<ul>
					{foreach from=$values.plugin_categories item=item key=key name=name}
						<li>
							<label>
								<input type="checkbox" name="plugin_category_{$item->pluginCategoryId}" {if in_array($item->pluginCategoryId, $values.plugin_category_mappings, true)} checked {/if} />
								{$item->displayName}
							</label>
						</li>
					{/foreach}
				</ul>
			</dd>

			<dt class="action"></dt>
			<dd class="action">
				<button>登録</button>
			</dd>
		</dl>
	</form>

{/block}
