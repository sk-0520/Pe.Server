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
				<li><a href="/management/version">バージョン設定</a></li>
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
				<li>
					<a href="/management/configuration">現在設定</a>
					<ul>
						<li>
							<a href="/management/configuration/edit">編集</a>
						</li>
					</ul>
				</li>
				<li>
					保守
					<ul>
						<li>
							<form method="post" action="/management/backup">
								{csrf}
								<button class="link">バックアップ</button>
							</form>
						</li>
						<li>
							<form method="post" action="/management/delete-old-data">
								{csrf}
								<button class="link">不要データ削除処理</submit>
							</form>
						</li>
						<li>
							<form method="post" action="/management/vacuum-access-log">
								{csrf}
								<button class="link">アクセスログ整理</submit>
							</form>
						</li>
						<li>
							<form method="post" action="/management/cache-rebuild">
								{csrf}
								<button class="link">キャッシュ再構築</submit>
							</form>
						</li>
						<li>
							<form method="post" action="/management/clear-deploy-progress">
								{csrf}
								<button class="link">デプロイ進捗ファイル破棄</submit>
							</form>
						</li>
						<li>
							管理
							<ul>
								<li>
									<a href="/management/control/user">
										ユーザー一覧
									</a>
								</li>
								<li>
									<a href="/management/control/backup">
										バックアップ一覧
									</a>
								</li>
							</ul>
						</li>
					</ul>
				</li>
			</ul>
		</li>
		<li>
			実行
			<ul>
				<li><a href="/management/database-maintenance">DBメンテナンス</a> (<a href="/management/database-download">DL</a>)</li>
				<li><a href="/management/php-evaluate">PHP実行</a></li>
				<li><a href="/management/mail-send">メール送信</a></li>
			</ul>
		</li>
		<li><a href="/management/markdown">Markdown</a></li>
	</ul>

{/block}
