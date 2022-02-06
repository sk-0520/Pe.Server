{extends file='default.tpl'}
{block name='TITLE'}セットアップ{/block}
{block name='BODY'}

<ul>
	{foreach from=$values.plugins item=item}
		<li>
			<a href="/plugin/{$item.plugin_id}">
				{if $item.registered}
					[あり]
				{else}
					[なし]
				{/if}
				{$item.plugin_name}
			</a>
		</li>
	{/foreach}
</ul>

<form action="/setting/default-plugin" method="post">
	<label><input name="delete" type="checkbox" />削除</label>
	<button>実行</button>
</form>

{/block}
