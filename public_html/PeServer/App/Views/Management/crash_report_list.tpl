{extends file='default.tpl'}
{block name='TITLE'}クラッシュレポート{/block}

{block name='HEAD'}{csrf type='id'}{/block}
{block name='DEFAULT_SCRIPT'}{asset file='/scripts/management_crash_report_list.js'}{/block}

{block name='BODY'}
	<template id="pg-delete-dialog">
		<div class="dialog crash-report-delete">
			<p>けす！</p>
		</div>
	</template>

	{if $values.pager->totalItemCount}
		<p class="search-count-area">
			<em class="count">{$values.pager->totalItemCount}</em>
			<span class="unit">件</em>
		</p>

		<table class="search-result crash-report-list-result">
			<thead>
				<tr>
					<th class="column-sequence">*</th>
					<th class="column-timestamp">日時</th>
					<th class="column-version">バージョン</th>
					<th class="column-exception-subject">例外</th>
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
						<td class="column-exception-subject">{$item->exceptionSubject}</td>
						<td class="column-detail"><a href="/management/crash-report/{$item->sequence}">詳細</a></td>
						<td class="column-delete"><button class="pg-delete" data-sequence="{$item->sequence}">削除</button></form>
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>

		{pager data=$values.pager href="/management/crash-report/page/<page_number>"}
	{else}
		<p class="search-not-found">データなし</p>
	{/if}

{/block}
