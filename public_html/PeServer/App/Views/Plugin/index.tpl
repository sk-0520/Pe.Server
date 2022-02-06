{extends file='default.tpl'}
{block name='TITLE'}プラグイン{/block}
{block name='BODY'}

<ul>
	{foreach from=$values.plugins item=item key=key name=name}
		<li>
			<a href="/plugin/{$item->pluginId}">{$item->displayName}</a>
		</li>
	{/foreach}
</ul>


{/block}
