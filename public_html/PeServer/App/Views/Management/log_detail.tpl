{extends file='default.tpl'}
{block name='TITLE'}ログ詳細: {$values.log_name}{/block}
{block name='BODY'}

	<dl>
		<dt>
			<ul class="inline">
				<li>
					<form method="post">
						<button>ダウンロード</button>
					</form>
				</li>
				<li><code data-clipboard="inline">{$values.log_file}</code></li>
			</ul>

		</dt>
		<dd>
			<pre data-clipboard="block">{$values.log_value}</pre>
		</dd>
	</dl>

{/block}
