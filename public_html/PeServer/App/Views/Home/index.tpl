{extends file='default.tpl'}
{block name='TITLE'}トップ{/block}
{block name='BODY'}

	<p>
		Pe のサーバーが必要な処理。
	</p>

	<ul>
		<li><a href='/plugin'>プラグイン</a></li>
		<li><a href='/api-doc'>API</a></li>
		<li>
			開発ドキュメント
			<ul>
				<li><a href='/public/api-doc/'>Doc: PeServer</a></li>
				<li><a href='/public/coverage/php/'>Code Coverage: PeServer</a></li>
				<li><a href='/public/coverage/script/lcov-report/'>Code Coverage: Script</a></li>
			</ul>
		</li>
		<li>
			ビルド状況
			<table>
				<tbody>
					<tr>
						<th>Current</th>
						<td>
							<a href="https://ci.appveyor.com/project/sk_0520/pe-server">
								<img src="https://ci.appveyor.com/api/projects/status/3nevme7nvotosxy5?svg=true" />
							</a>
						</td>
					</tr>
					<tr>
						<th>Master</th>
						<td>
							<a href="https://ci.appveyor.com/project/sk_0520/pe-server/branch/master">
								<img src="https://ci.appveyor.com/api/projects/status/3nevme7nvotosxy5/branch/master?svg=true" />
							</a>
						</td>
					</tr>
				</tbody>
			</table>
		</li>
	</ul>

	{if \PeServer\Core\Environment::isDevelopment()}
		<h2>dev</h2>
		<ul>
			<li><a href="/dev/exception">exception</a></li>
		</ul>
	{/if}


{/block}
