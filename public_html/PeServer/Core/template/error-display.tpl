{$show_exception = PeServer\Core\Environment::isDevelopment() && !is_null($values.throwable)}
<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8"/>
		<title>Pe.Server.Core: Error</title>
		{asset file='./error-display.css' include='true'}
	</head>
	<body>
		<main id="main">
			<dl class="error">
				<dt>ステータスコード</dt>
				<dd><code>{$status->code()}</code></dd>

				<dt>エラーコード</dt>
				<dd><code>{$values.error_number}</code></dd>

				<dt>メッセージ</dt>
				<dd>{$values.message|default:'なし'}</dd>

				{if $show_exception}
					<dt>例外</dt>
					<dd><code>{get_class($values.throwable)}</code></dd>
				{/if}

				<dt>ソース</dt>
				<dd><code>{$values.file}</code>:<code>{$values.line_number}</code></dd>

				{if PeServer\Core\Environment::isDevelopment() && !is_null($values.throwable)}
					{$throwable = $values.throwable->getPrevious()}
					{if $throwable}
						<dt>ラップされた例外</dt>
						<dd class="throwable">
							{$level = 0}
							{while $throwable}
								<dl style="margin-left: {$level++ * 10}px">
									<dt>例外</dt>
									<dd><code>{get_class($throwable)}</code></dd>

									<dt>エラーコード</dt>
									<dd><code>{$throwable->getCode()}</code></dd>

									<dt>メッセージ</dt>
									<dd><code>{$throwable->getMessage()}</code></dd>

								</dl>
								{$throwable = $throwable->getPrevious()}
							{/while}
						</dd>
					{/if}

					<dt>スタックトレース</dt>
					<dd>
						<ol class="stacktrace">
							{foreach from=$values.throwable->getTrace() item=item}
								<li>
									<code>{$item.file}:{$item.line}</code>
									<table>
										{if isset($item.object) }
											<tr>
												<td>object</dt>
												<td><code>{$item.object}</code></dt>
											</tr>
										{/if}
										<tr>
											<td>function</dt>
											<td>
												{if isset($item.class) }
													<code>{$item.class}</code>
												{/if}
												<code title="type">{$item.type}</code>
												<code>{$item.function}</code>
											</dt>
										</tr>
										{if isset($item.args) }
											{foreach from=$item.args item=arg key=arg_index}
												<tr class="args">
													<td>arg: {$arg_index + 1}</td>
													<td>
														{if is_object($arg) || is_array($arg) }
															{foreach from=$arg item=values key=key}
																	<pre class="code">[{$key}] => {$values|@var_dump}</pre>
															{/foreach}
														{else}
															<code>{$arg}</code>
														{/if}
														</ol>
													</td>
												</tr>
											{/foreach}
										{/if}
									</table>
								</li>
							{/foreach}
						</ol>
					</dd>
					<hr />
					<details>
						<summary>var_dump</summary>
						<pre>{$values.throwable|@var_dump}</pre>
					</details>
				{/if}
			</dl>
		</main>
	</body>
</html>
