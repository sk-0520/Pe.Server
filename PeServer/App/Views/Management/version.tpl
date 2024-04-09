{extends file='default.tpl'}
{block name='TITLE'}バージョン設定{/block}
{block name='DEFAULT_SCRIPT'}{asset file='/scripts/management_version.js'}{/block}
{block name='BODY'}

	<form action="/management/version" method="post">
		{csrf}

		<dl class="input">
			<dt>バージョン</dt>
			<dd>
				{input_helper key='version' type="text" class="edit" pattern="[0-9]+\.[0-9]+\.[0-9]+"}
			</dd>

			<dt>
				<button id="command_get_versions" type="button" data-release_url="{$values.release_url}">取得</button>
			</dt>
			<dd>
				<pre id="versions">未取得</pre>
			</dd>

			<dt class="action"></dt>
			<dd class="action">
				<button>登録</button>
			</dd>
		</dl>

	</form>

{/block}
