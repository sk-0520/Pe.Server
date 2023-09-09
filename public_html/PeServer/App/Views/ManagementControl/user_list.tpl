{extends file='default.tpl'}
{block name='TITLE'}ユーザー一覧{/block}
{block name='BODY'}

	<table>
		<thead>
			<tr>
				<th>ユーザーID</th>
				<th>状態</th>
				<th>名前</th>
				<th>ログインID</th>
				<th>レベル</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$values.users item=item}
				<tr>
					<td data-clipboard="block"><code>{$item->userId}</code></td>
					<td>{$item->state}</td>
					<td data-clipboard="block"><code>{$item->name}</code></td>
					<td data-clipboard="block"><code>{$item->loginId}</code></td>
					<td>{$item->level}</td>
					<td class="mute">未実装</td>
				</tr>
			{foreachelse}
				<tr>
					<td colspan="6">
						誰もおらん(こんなことない)。
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>

{/block}
