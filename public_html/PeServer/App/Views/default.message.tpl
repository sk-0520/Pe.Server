{if isset($values.temp_messages) && !empty($values.temp_messages)}
	<div class="common messages">
	<ul>
	{foreach from=$values.temp_messages item=item key=key name=name}
		<li data-index="{$key}">{$item}</li>
	{/foreach}
	</ul>
	</div>
{/if}
