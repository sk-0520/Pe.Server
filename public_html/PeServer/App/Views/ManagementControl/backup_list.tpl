{extends file='default.tpl'}
{block name='TITLE'}バックアップ一覧{/block}
{block name='BODY'}

	<dl>
		<dt>ディレクトリ</dt>
		<dd>
			<code data-clipboard="inline">{$values.directory}</code>
		</dd>

		<dt>ファイル</dt>
		<dd>
			<ol>
				{foreach from=$values.items item=item}
					<li>
						<a href="/management/control/backup/{$item.name}">{$item.name}</a>
						<span title="{$item.size} byte">{$item.human_size}</span>
					</li>
				{foreachelse}
					<li>ない</li>
				{/foreach}
			</ol>

		</dd>
	</dl>

{/block}
