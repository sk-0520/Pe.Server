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
				<li><a href='/public/coverage/php/uit/'>Code Coverage: PeServer</a></li>
				<li><a href='/public/coverage/script/lcov-report/'>Code Coverage: Script</a></li>
			</ul>
		</li>
		<li>
			ビルド状況
			<table>
				<tbody>
					<tr>
						<th rowspan="2">Pe</th>
						<th>Current</th>
						<td>
							<a href="https://github.com/sk-0520/Pe/actions/workflows/build-ci-only.yml">
								<img class="page-user-index-ci {$environment->get()}" {if $environment->isProduction()} src="https://github.com/sk-0520/Pe/actions/workflows/build-ci-only.yml/badge.svg" {/if} />
							</a>
						</td>
					</tr>
					<tr>
						<th>Master</th>
						<td>
							<a href="https://github.com/sk-0520/Pe/actions/workflows/build-ci-cd.yml">
								<img class="page-user-index-ci {$environment->get()}" {if $environment->isProduction()} src="https://github.com/sk-0520/Pe/actions/workflows/build-ci-cd.yml/badge.svg?branch=master" {/if} />
							</a>
						</td>
					</tr>

					<tr>
						<th rowspan="2">Pe.Server</th>
						<th>Current</th>
						<td>
							<a href="https://github.com/sk-0520/Pe.Server/actions">
								<img class="page-user-index-ci {$environment->get()}" {if $environment->isProduction()} src="https://github.com/sk-0520/Pe.Server/actions/workflows/build-works.yml/badge.svg" {/if} />
							</a>
						</td>
					</tr>
					<tr>
						<th>Master</th>
						<td>
							<a href="https://github.com/sk-0520/Pe.Server/actions?query=branch%3Amaster">
								<img class="page-user-index-ci {$environment->get()}" {if $environment->isProduction()} src="https://github.com/sk-0520/Pe.Server/actions/workflows/build-works.yml/badge.svg?branch=master" {/if} />
							</a>
						</td>
					</tr>
				</tbody>
			</table>
		</li>
		<li><a href='/tool'>ツールとか</a></li>
	</ul>

	{if $environment->isDevelopment()}
		<h2>dev</h2>
		<ul>
			<li><a href="/dev/exception">exception</a></li>
		</ul>
	{/if}


{/block}
