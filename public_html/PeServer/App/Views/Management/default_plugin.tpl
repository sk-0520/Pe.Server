{extends file='default.tpl'}
{block name='TITLE'}標準プラグイン{/block}
{block name='BODY'}

	<ul>
		{foreach from=$values.plugins item=item}
			<li>
				<a href="/plugin/{$item.item->pluginId}">
					{if $item.registered}
						[あり]
					{else}
						[なし]
					{/if}
					{$item.item->pluginName}
				</a>
			</li>
		{/foreach}
	</ul>

	<form action="/management/default-plugin" method="post">
		{csrf}

		<label><input name="delete" type="checkbox" />削除</label>
		<button>実行</button>
	</form>

{/block}
