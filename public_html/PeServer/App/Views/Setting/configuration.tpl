{extends file='default.tpl'}
{block name='TITLE'}現在設定{/block}
{block name='BODY'}

<h2>setting.json</h2>
<pre>{$values.config|@var_dump}</pre>

<h2>database</h2>
{strip}
	{foreach from=$values.tables item=item key=key}
		<h3>
			<strong>{$item.name}</strong>
			&nbsp;
			(<code data-count>{$item.table.rows|count}</code>)
		</h3>
		<table class="basic">
			<thead>
				<tr>
					{foreach from=$item.table.columns item=column}
						<th>
							{$column.name}
						</th>
					{/foreach}
				</tr>
			</thead>
			<tbody>
				{foreach from=$item.table.rows item=row}
					<tr>
						{foreach from=$row item=cell}
							<td>
								{$cell}
							</td>
						{/foreach}
					</tr>
				{/foreach}
			</tbody>
		</table>
	{/foreach}
{/strip}

{/block}
