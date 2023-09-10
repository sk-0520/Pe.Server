{extends file='default.tpl'}
{block name='TITLE'}現在設定{/block}
{block name='BODY'}

	<form class="page-management-config-edit" action="/management/configuration/edit" method="post">
		{csrf}

		<dl class="input">
			<dt>setting.{$values.env_name}.json</dt>
			<dt><code>{$values.path}</code></dt>
			<dd>
				{input_helper key="json" type="textarea" class="edit json" autofocus="true" required="true"}
			</dd>
			<dt class="action"></dt>
			<dd class="action">
				<button>更新</button>
			</dd>
		</dl>
	</form>

{/block}
