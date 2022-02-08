{extends file='default.tpl'}
{block name='TITLE'}プラグイン: {$values.plugin->displayName}{/block}
{block name='BODY'}

<dl>
	<dt>プラグインID</dt>
	<dd><code data-clipboard="inline">{$values.plugin->pluginId}</code></dd>

	<dt>プラグイン内部名</dt>
	<dd><code data-clipboard="inline">{$values.plugin->pluginName}</code></dd>

	<dt>プラグイン表示名</dt>
	<dd>{$values.plugin->displayName}</dd>

<!--
	<dt></dt>
	<dd></dd>

	<dt></dt>
	<dd></dd>

	<dt></dt>
	<dd></dd>

	<dt></dt>
	<dd></dd>
-->

	<dt>説明</dt>
	<dd>{markdown}{$values.plugin->description}{/markdown}</dd>
</dl>


{/block}
