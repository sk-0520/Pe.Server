<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8"/>
		<title>Pe.Server.Core: Error</title>
		<style>

		</style>
	</head>
	<body>
		<main id="main">
			<dl>
				<dt>ステータスコード</dt>
				<dd><code>{$status->code()}</code></dd>

				<dt>エラーコード</dt>
				<dd><code>{$values.error_number}</code></dd>

				<dt>メッセージ</dt>
				<dd>{$values.message|default:'なし'}</dd>

				<dt>ソース</dt>
				<dd><code>{$values.file}</code>:<code>{$values.line_number}</code></dd>
			</dl>

			{if PeServer\Core\Environment::isDevelopment() && !is_null($values.throwable)}
				<pre>{$values.throwable|@var_dump}</pre>
			{/if}
		</main>
	</body>
</html>
