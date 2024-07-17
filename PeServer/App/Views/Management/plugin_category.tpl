{extends file='default.tpl'}
{block name='TITLE'}プラグインカテゴリ{/block}

{block name='HEAD'}{csrf type='id'}{/block}
{block name='DEFAULT_SCRIPT'}{asset file='/scripts/management_plugin_category.js'}{/block}

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
				<tr data-plugin-category-id="{$item->pluginCategoryId}">
					<td><input class="edit" type="text" readonly value="{$item->pluginCategoryId}" /></td>
					<td><input name="display-name" class="edit" type="text" value="{$item->displayName}" /></td>
					<td><input name="description" class="edit" type="text" value="{$item->description}" /></td>
					<td><button name="update">📝</button></td>
					<td><button name="delete">❌</button></td>
				</tr>
			{/foreach}
		</tbody>

		<tfoot>
			<tr>
				<td><input id="category_add_id" class="edit" type="text" placeholder="一意なカテゴリID" /></td>
				<td><input id="category_add_display" class="edit" type="text" /></td>
				<td><input id="category_add_description" class="edit" type="text" /></td>
				<td colspan="2"><button id="category_add_submit">✅追加</button></td>
			</tr>
		</tfoot>
	</table>

{/block}
