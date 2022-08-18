{extends file='default.tpl'}
{block name='TITLE'}設定{/block}
{block name='BODY'}

<ul>
	<li><a href="/setting/log">ログ</a></li>
	<li>
		プラグイン
		<ul>
			<li><a href="/setting/default-plugin">標準プラグイン登録</a></li>
			<li><a href="/setting/plugin-category">プラグインカテゴリ</a></li>
		</ul>
	</li>
	<li>
		設定
		<ul>
			<li><a href="/setting/environment">環境情報</a></li>
			<li><a href="/setting/configuration">現在設定</a></li>
		</ul>
	</li>
	<li>
		実行
		<ul>
			<li><a href="/setting/database-maintenance">DBメンテナンス</a></li>
			<li><a href="/setting/php-evaluate">PHP実行</a></li>
		</ul>
	</li>
	<li><a href="/setting/cache-rebuild">キャッシュ再構築</a></li>
	<li><a href="/setting/markdown">Markdown</a></li>
</ul>

{/block}
