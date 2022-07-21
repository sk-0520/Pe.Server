{extends file='default.tpl'}
{block name='TITLE'}DBメンテナンス{/block}
{block name='BODY'}

<h2>入力</h2>
<form class="page-setting-database-maintenance" action="/setting/database-maintenance" method="post">
	{csrf}

	<dl class="input">
		<dt>SQL</dt>
		<dd>
			{input_helper key='database_maintenance_statement' type="textarea" class="edit" required="true"}
		</dd>

		<dt class="action">実行</dt>
		<dd class="action">
			<button>submit</button>
		</dd>
	</dl>

</form>

{if $values.executed}
	{if is_array($values.result)}
		{if count($values.result)}
			<h2>結果: {count($values.result)}</h2>
			<table class="basic">
				<thead>
					<tr>
						<th>-</th>
						{foreach from=$values.result[0] item=item key=key}
							<th data-clipboard="inline">{$key}</th>
						{/foreach}
					</tr>
				</thead>
				<tbody>
					{foreach from=$values.result item=row key=numner}
						<tr>
							<th>{$numner + 1}</th>
							{foreach from=$row item=cell key=key}
								<td data-clipboard="inline">{$cell|escape:'html'|nl2br nofilter}</td>
							{/foreach}
						</tr>
					{/foreach}
				</tbody>
			</table>
		{else}
			<h2>結果: 0</h2>
			<p>結果なし。</p>
		{/if}
	{elseif is_int($values.result)}
			<h2>結果</h2>
			<code>{$values.result}</code>
	{else}
		<h2>結果: エラー</h2>
		<pre data-clipboard="block">{$values.result}</pre>
			<details>
				<summary>詳細ダンプ</summary>
				<pre data-clipboard="block">{$values.result|@var_dump}</pre>
			</details>
	{/if}
{/if}

{/block}
