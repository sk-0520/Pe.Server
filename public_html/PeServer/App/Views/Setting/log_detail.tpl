{extends file='default.tpl'}
{block name='TITLE'}セットアップ{/block}
{block name='BODY'}

<code data-clipboard="inline">{$values.log_file}</code>
<pre>{$values.log_value}</pre>

{/block}
