{extends file='default.tpl'}
{block name='TITLE'}セットアップ{/block}
{block name='BODY'}

{if count($values.log_files)}
	<dl>
		<dt>ディレクトリ</dt>
		<dd>
			{$values.directory}
		</dd>

		<dt>ファイル</dt>
		<dd>
			<ul>
				{foreach from=$values.log_files item=item}
					<li>
						<a href="/setting/log/{$item.name}">{$item.name}</a>
						<span title="{$item.size}">{$item.human_size}</span>
					</li>
				{/foreach}
			</ul>
		</dd>
	</dl>
{else}
	<p>ファイルないよ</p>
{/if}

{/block}
