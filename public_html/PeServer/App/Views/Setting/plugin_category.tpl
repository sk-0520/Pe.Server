{extends file='default.tpl'}
{block name='TITLE'}プラグインカテゴリ{/block}

{block name='DEFAULT_SCRIPT'}{asset file='/scripts/plugin_category.js'}{/block}

{block name='BODY'}

<table>
	<thead>
		<tr>
			<th>カテゴリID</th>
			<th>カテゴリ名</th>
			<th>更新</th>
			<th>削除</th>
		</tr>
	</thead>

	{foreach from=$values.categories item=item}
		<tbody id="category_items">
			<tr>
				<td data-plugin_category_id="{$item.plugin_category_id}"><code>{$item.plugin_category_id}</code></td>
				<td><input id="category_add_id" type="text" value="{$item.display_name}" /></td>
				<td><button name="update">📝</button></td>
				<td><button name="delete">❌</button></td>
			</tr>
		</tbody>
	{/foreach}

	<tfoot>
		<tr>
			<th><input id="category_add_id" type="text" /></th>
			<td><input id="category_add_display" type="text" /></td>
			<td colspan="2"><button id="category_add_submit">追加</button></td>
		</tr>
	</tfoot>
</table>

{/block}
