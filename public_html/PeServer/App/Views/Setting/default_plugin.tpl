{extends file='default.tpl'}
{block name='TITLE'}セットアップ{/block}
{block name='BODY'}

<span style="font-size: 200pt">💩</span>
<hr />

<ul>
	{foreach from=$values.plugins item=item}
		<li>
			<a href="/plugins/{$item.plugin_id}">{$item.plugin_name}</a>
		</li>
	{/foreach}
</ul>

<form action="/setting/default-plugin" method="post">
	<button>登録</button>
</form>

{/block}
