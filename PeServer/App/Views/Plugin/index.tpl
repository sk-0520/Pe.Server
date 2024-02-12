{extends file='default.tpl'}
{block name='TITLE'}プラグイン{/block}
{block name='BODY'}

	{if $values.link_default_plugin}
		<p>
			<a href="/management/default-plugin">標準プラグイン</a>
		</p>
	{/if}

	<ul>
		{foreach from=$values.plugins item=item key=key name=name}
			<li>
				<a href="/plugin/{$item->pluginId}">{$item->displayName}</a>
			</li>
		{/foreach}
	</ul>


{/block}
