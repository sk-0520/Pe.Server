{extends file='default.tpl'}
{block name='TITLE'}トップ{/block}
{block name='BODY'}

<p>
	Pe のサーバーが必要な処理。
</p>

<ul>
	<li><a href="/plugin">プラグイン</a></li>
	<li><a href="/api-doc">API</a></li>
	<li>
		開発ドキュメント
		<ul>
			<li><a href="/public/api-doc/">Doc: PeServer</a></li>
			<li><a href="/public/coverage/php/">Code Coverage: PeServer</a></li>
			<li><a href="/public/coverage/script/lcov-report/">Code Coverage: Script</a></li>
		</ul>
	</li>
</ul>

{if \PeServer\Core\Environment::isDevelopment()}
	<h2>dev</h2>
	<ul>
		<li><a href="/dev/exception">exception</a></li>
	</ul>
{/if}


{/block}

