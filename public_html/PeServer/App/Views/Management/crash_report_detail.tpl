{extends file='default.tpl'}
{block name='TITLE'}クラッシュレポート: {$values.detail->sequence}{/block}
{block name='BODY'}

	<dl>
		<dt>シーケンス</dt>
		<dd>{$values.detail->sequence}</dd>

		<dt>タイムスタンプ</dt>
		<dd>{$values.detail->timestamp}</dd>

		<dt>IPアドレス</dt>
		<dd>{$values.detail->ipAddress}</dd>

		<dt>バージョン</dt>
		<dd>{$values.detail->version}</dd>

		<dt>リビジョン</dt>
		<dd>{$values.detail->revision}</dd>

		<dt>ビルド</dt>
		<dd>{$values.detail->build}</dd>

		<dt>ユーザーID</dt>
		<dd><code>{$values.detail->userId}</code></dd>

		<dt>例外</dt>
		<dd>{code}{$values.detail->exception nofilter}{/code}</dd>

		<dt>メールアドレス</dt>
		<dd>
			{if $values.email}
				<code>{$values.email}</code>
			{else}
				<span class="mute">なし</span>
			{/if}
		</dd>

		<dt>コメント</dt>
		<dd>
			{if $values.detail->comment}
				{code}{$values.detail->comment nofilter}{/code}
			{else}
				<span class="mute">なし</span>
			{/if}
		</dd>

		<dt>レポート</dt>
		<dd>
			<a download="crash-report-{$values.detail->sequence}.json" href="data:application/json;base64,{$values.report nofilter}">ダウンロード</a>
		</dd>
	</dl>

	<section>
		<form method="post" action="/management/crash-report/{$values.detail->sequence}">
			<h2>開発用</h2>
			<dl class="input">
				<dt>メモ</dt>
				<dd>
					{input_helper key='developer-comment' type="textarea" class="edit developer-comment"}
				</dd>

				<dt class="action">実行</dt>
				<dd class="action">
					<button>保存</button>
				</dd>
			</dl>
		</form>
	</section>

{/block}
