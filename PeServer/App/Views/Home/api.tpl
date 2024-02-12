{extends file='default.tpl'}
{block name='TITLE'}API{/block}
{block name='BODY'}

	<section>
		<h2>APIドキュメント</h2>
		<p>
			<a href="{$values.api_doc_url}">Pe.Server</a>
		</p>
		<p>
			上記APIドキュメント最新版に従う。<br />
			時期開発版デプロイ前に最新化される可能性がよくある。
		</p>
	</section>

	<section>
		<h2>共通事項</h2>

		<table>
			<tbody>
				<tr>
					<th>X-API-KEY</th>
					<td>APIキー</td>
					<td>ユーザーページのAPIから取得</td>
				</tr>
				<tr>
					<th>X-SECRET-KEY</th>
					<td>シークレットキー</td>
					<td>API生成時にのみ出力される</td>
				</tr>
			</tbody>
		</table>
	</section>

{/block}
