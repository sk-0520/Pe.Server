{extends file='default.tpl'}
{block name='TITLE'}管理{/block}
{block name='BODY'}

	<ul>
		<li><a href="/management/log">ログ</a></li>
		<li>
			Pe
			<ul>
				<li><a href="/management/feedback">フィードバック</a></li>
				<li><a href="/management/crash-report">クラッシュレポート</a></li>
			</ul>
		</li>
		<li>
			プラグイン
			<ul>
				<li><a href="/management/default-plugin">標準プラグイン登録</a></li>
				<li><a href="/management/plugin-category">プラグインカテゴリ</a></li>
			</ul>
		</li>
		<li>
			設定
			<ul>
				<li><a href="/management/environment">環境情報</a></li>
				<li><a href="/management/configuration">現在設定</a></li>
				<li>
					保守
					<ul>
						<li>
							<form method="post" action="/management/backup">
								{csrf}
								<button class="link">バックアップ</submit>
							</form>
						</li>
						<li>
							<form method="post" action="/management/cache-rebuild">
								{csrf}
								<button class="link">キャッシュ再構築</submit>
							</form>
						</li>
					</ul>
				</li>
			</ul>
		</li>
		<li>
			実行
			<ul>
				<li><a href="/management/database-maintenance">DBメンテナンス</a></li>
				<li><a href="/management/php-evaluate">PHP実行</a></li>
			</ul>
		</li>
		<li><a href="/management/markdown">Markdown</a></li>
	</ul>

{/block}
