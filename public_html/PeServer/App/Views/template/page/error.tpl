{extends file='default.tpl'}
{block name='TITLE'}エラー: {$status->code()}{/block}
{block name='BODY'}

<p>
	問題が発生しました。
</p>

<dl>
	<dt>ステータスコード</dt>
	<dd><code>{$status->getCode()}</code></dd>

	<dt>エラーコード</dt>
	<dd><code>{$values.error_number}</code></dd>

	<dt>メッセージ</dt>
	<dd>{$values.message|default:'なし'}</dd>
</dl>

{/block}
