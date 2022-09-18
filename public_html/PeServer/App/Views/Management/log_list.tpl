{extends file='default.tpl'}
{block name='TITLE'}ログ一覧{/block}

{block name='HEAD'}{csrf type='id'}{/block}
{block name='DEFAULT_SCRIPT'}{asset file='/scripts/management_log_list.js'}{/block}

{block name='BODY'}

	{if count($values.log_files)}
		<dl>
			<dt>ディレクトリ</dt>
			<dd>
				<code data-clipboard="inline">{$values.directory}</code>
			</dd>

			<dt>ファイル</dt>
			<dd>
				<ol>
					{foreach from=$values.log_files item=item}
						<li>
							<button class="pg-delete" title="削除" data-name={$item.name}>🗑️</button>
							<a href="/management/log/{$item.name}">{$item.name}</a>
							<span title="{$item.size} byte">{$item.human_size}</span>
						</li>
					{/foreach}
				</ol>
			</dd>
		</dl>
	{else}
		<p>ファイルないよ</p>
	{/if}

{/block}
