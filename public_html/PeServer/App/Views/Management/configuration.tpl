{extends file='default.tpl'}
{block name='TITLE'}現在設定{/block}
{block name='BODY'}

	<h2>setting.json</h2>
	<pre data-clipboard="block">{$values.config|var_dump}</pre>

{/block}
