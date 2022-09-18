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
		<dd>{code language="json"}{$values.report nofilter}{/code}</dd>
	</dl>

{/block}
