{extends file='default.tpl'}
{block name='TITLE'}ログ詳細: {$values.log_name}{/block}
{block name='BODY'}

	<code data-clipboard="inline">{$values.log_file}</code>
	<pre data-clipboard="block">{$values.log_value}</pre>

{/block}
