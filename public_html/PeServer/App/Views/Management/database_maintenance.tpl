{extends file='default.tpl'}
{block name='TITLE'}DBメンテナンス{/block}
{block name='DEFAULT_SCRIPT'}{asset file='/scripts/management_database_maintenance.js'}{/block}
{block name='BODY'}

<h2>入力</h2>
<form class="page-setting-database-maintenance" action="/management/database-maintenance" method="post">
	{csrf}

	<dl class="input">
		<dt>SQL</dt>
		<dd>
			{input_helper key='database_maintenance_statement' type="textarea" class="edit" required="true"}
			<details id=tables>
				<summary>TABLE</summary>
				<ul class="inline">
					{foreach from=$values.tables item=item key=key name=name}
						<li class="inline">
							<button class="pg-table" type="button" data-table={$item.name} data-columns="{$item.columns|json_encode}">{$item.name}</button>
						</li>
					{/foreach}
				</ul>
			</details>
		</dd>

		<dt class="action">実行</dt>
		<dd class="action">
			<button>submit</button>
		</dd>
	</dl>

</form>

{if $values.executed}
	{if $values.result instanceof PeServer\Core\Database\DatabaseTableResult}
		<h2>結果: {$values.result->getRowsCount()}</h2>
		<table>
			<thead>
				<tr>
					<th>-</th>
					{foreach from=$values.result->columns item=item key=key}
						<th data-clipboard="inline">{$item->name}</th>
					{/foreach}
				</tr>
			</thead>
			<tbody>
				{foreach from=$values.result->rows item=row key=numner}
					<tr>
						<th>{$numner + 1}</th>
						{foreach from=$row item=cell key=key}
							<td data-clipboard="inline">{$cell|escape:'html'|nl2br nofilter}</td>
						{/foreach}
					</tr>
				{/foreach}
			</tbody>
		</table>

		<h3>影響件数</h3>
		<pre>{$values.result->getResultCount()}</pre>


	{else}
		<h2>結果: エラー</h2>
		<pre data-clipboard="block">{$values.result}</pre>
			<details>
				<summary>詳細ダンプ</summary>
				<pre data-clipboard="block">{$values.result|@var_dump}</pre>
			</details>
		</pre>
	{/if}
{/if}

{/block}
