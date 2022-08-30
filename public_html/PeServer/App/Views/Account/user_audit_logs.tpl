{extends file='default.tpl'}
{block name='TITLE'}監査ログ{/block}
{block name='BODY'}

	<table>
		<thead>
			<tr>
				<th>*</th>
				<th>TIMESTAMP</th>
				<th>EVENT</th>
				<th>INFO</th>
				<th>IP ADDR</th>
				<th>UA</th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$values.logs->rows item=item key=key name=name}
				<tr>
					<td>{$key}</td>
					<td>{$item.timestamp}Z</td>
					<td>{$item.event}</td>
					<td>{code language='json'}{$item.info nofilter}{/code}</td>
					<td>{$item.ip_address}</td>
					<td>{$item.user_agent}</td>
				</tr>
			{/foreach}
		</tbody>
	</table>

{/block}
