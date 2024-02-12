{extends file='default.tpl'}
{block name='TITLE'}プラグイン: {$values.plugin->displayName}{/block}
{block name='BODY'}

	<dl>
		<dt>カテゴリ</dt>
		<dd>
			{if empty($values.categories)}
				<span class="mute">プラグインカテゴリ未登録</span>
			{else}
				<ul class="inline">
					{foreach from=$values.categories item=item key=key name=name}
						<li>
							{$item->categoryName}
						</li>
					{/foreach}
				</ul>
			{/if}
		</dd>

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
		<dd>{markdown}{$values.plugin->description nofilter}{/markdown}</dd>
	</dl>


{/block}
