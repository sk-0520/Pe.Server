{extends file='default.tpl'}
{block name='TITLE'}フィードバック{/block}

{block name='HEAD'}{csrf type='id'}{/block}
{block name='DEFAULT_SCRIPT'}{asset file='/scripts/feedback.js'}{/block}

{block name='BODY'}
	<template id="pg-delete-dialog">
		<div class="dialog feedback-delete">
			<p>けす！</p>
		</div>
	</template>

	{if $values.total_count}
		<p class="search-count">{$values.total_count}</p>

		<table class="search-result feedback-list-result">
			<thead>
				<tr>
					<th class="column-sequence">*</th>
					<th class="column-timestamp">日時</th>
					<th class="column-version">バージョン</th>
					<th class="column-kind">種類</th>
					<th class="column-subject">件名</th>
					<th class="column-detail">詳細</th>
					<th class="column-delete">削除</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$values.items item=item key=key name=name}
					<tr>
						<td class="column-sequence">{$item->sequence}</td>
						<td class="column-timestamp">{$item->timestamp}</td>
						<td class="column-version">{$item->version}</td>
						<td class="column-kind">{$item->kind}</td>
						<td class="column-subject">{$item->subject}</td>
						<td class="column-detail"><a href="/management/feedback/detail/{$item->sequence}">詳細</a></td>
						<td class="column-delete"><button class="pg-delete" data-sequence="{$item->sequence}">削除</button></form>
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>

		{pager data=$values.pager href="/management/feedback/page/<page_number>"}
	{else}
		<p class="search-not-found">データなし</p>
	{/if}

{/block}
