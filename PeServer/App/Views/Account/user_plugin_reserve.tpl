{extends file='default.tpl'}
{block name='TITLE'}プラグイン 登録{/block}
{block name='DEFAULT_SCRIPT'}{asset file='/scripts/plugin_edit.js'}{/block}

{block name='BODY'}

	<form class="page-account-plugin" action="/account/user/plugin/reserve" method="post">
		{csrf}

		<dl class="input">
			<dt>
				プラグインID
				<ul class="helper">
					<li><button id="pg-plugin-id-auto-generate" class="action sub" type="button">自動生成</button></li>
				</ul>
			</dt>
			<dd>
				{input_helper id='pg-plugin-id' key='account_plugin_reserve_plugin_id' type="text" class="edit"}
			</dd>

			<dt>プラグイン内部名</dt>
			<dd>
				{input_helper key='account_plugin_reserve_plugin_name' type="text" class="edit"}
			</dd>

			<dt class="action"></dt>
			<dd class="action">
				<button>登録</button>
			</dd>
		</dl>
	</form>

{/block}
