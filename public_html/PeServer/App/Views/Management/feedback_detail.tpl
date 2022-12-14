{extends file='default.tpl'}
{block name='TITLE'}フィードバック: {$values.detail->sequence}{/block}
{block name='BODY'}

	<dl>
		<dt>シーケンス</dt>
		<dd>{$values.detail->sequence}</dd>

		<dt>日時</dt>
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

		<dt>初回実行日時</dt>
		<dd>{$values.detail->firstExecuteTimestamp}</dd>

		<dt>初回実行バージョン</dt>
		<dd>{$values.detail->firstExecuteVersion}</dd>

		<dt>プロセス(実行CPU)</dt>
		<dd>{$values.detail->process}</dd>

		<dt>プラットフォーム(OS CPU)</dt>
		<dd>{$values.detail->platform}</dd>

		<dt>OS</dt>
		<dd>{$values.detail->os}</dd>

		<dt>CLR</dt>
		<dd>{$values.detail->clr}</dd>

		<dt>種別</dt>
		<dd>{$values.detail->kind}</dd>

		<dt>件名</dt>
		<dd>{$values.detail->subject}</dd>

		<dt>コメント</dt>
		<dd>{markdown}{$values.detail->content nofilter}{/markdown}</dd>
	</dl>

{/block}
