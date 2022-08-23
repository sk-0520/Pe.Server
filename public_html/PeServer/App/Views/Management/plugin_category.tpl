{extends file='default.tpl'}
{block name='TITLE'}プラグインカテゴリ{/block}

{block name='DEFAULT_SCRIPT'}{asset file='/scripts/plugin_category.js'}{/block}

{block name='BODY'}

<table>
	<thead>
		<tr>
			<th>カテゴリID</th>
			<th>カテゴリ名</th>
			<th>説明</th>
			<th>更新</th>
			<th>削除</th>
		</tr>
	</thead>

	<tbody id="category_items">
		{foreach from=$values.categories item=item}
			<tr data-plugin-category-id="{$item.plugin_category_id}">
				<td><input readonly value="{$item.plugin_category_id}" /></td>
				<td><input name="display-name" type="text" value="{$item.display_name}" /></td>
				<td><input name="description" type="text" value="{$item.description}" /></td>
				<td><button name="update">📝</button></td>
				<td><button name="delete">❌</button></td>
			</tr>
		{/foreach}
	</tbody>

	<tfoot>
		<tr>
			<th><input id="category_add_id" type="text" placeholder="一意なプラグインID" /></th>
			<td><input id="category_add_display" type="text" /></td>
			<td><input id="category_add_description" type="text" /></td>
			<td colspan="2"><button id="category_add_submit">✅追加</button></td>
		</tr>
	</tfoot>
</table>

{/block}
