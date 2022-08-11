{$show_exception = PeServer\Core\Environment::isDevelopment() && !is_null($values.throwable)}
<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8"/>
		<title>Pe.Server.Core: Error</title>
		{asset file='./error-display.css' include='true'}
		{if PeServer\Core\Environment::isDevelopment() && !is_null($values.throwable)}
			{asset file='./prism.css' include='true'}
		{/if}
	</head>
	<body>
		<main id="main">
			<dl class="error">
				<dt>ステータスコード</dt>
				<dd><code>{$status->getCode()}</code></dd>

				<dt>エラーコード</dt>
				<dd><code>{$values.error_number}</code></dd>

				<dt>メッセージ</dt>
				<dd>{$values.message|default:'なし'}</dd>

				{if $show_exception}
					<dt>例外</dt>
					<dd><code>{get_class($values.throwable)}</code></dd>
				{/if}

				{if PeServer\Core\Environment::isDevelopment() && !is_null($values.throwable)}
					<dt>GET</dt>
					<dd>
						<details>
							<summary>展開</summary>
							<pre>{$smarty.get|@var_dump}</pre>
						</details>
					</dd>

					<dt>POST</dt>
					<dd>
						<details>
							<summary>展開</summary>
							<pre>{$smarty.post|@var_dump}</pre>
						</details>
					</dd>

					<dt>Cookie</dt>
					<dd>
						<details>
							<summary>展開</summary>
							<pre>{$smarty.cookies|@var_dump}</pre>
						</details>
					</dd>

					{if isset($smarty.session)}
						<dt>Session</dt>
						<dd>
							<details>
								<summary>展開</summary>
								<pre>{$smarty.session|@var_dump}</pre>
							</details>
						</dd>
					{/if}
				{/if}

				<dt>ソース</dt>
				<dd>
				<code>{$values.file}</code>:<code>{$values.line_number}</code>
					{if PeServer\Core\Environment::isDevelopment() && !is_null($values.throwable)}
						{assign var="file" value=$values.cache[$values.file]}
						<pre id="throw" class="source" data-line="{$values.line_number}"><code class="language-php line-numbers">{$file}</code></pre>
					{/if}
				</dd>

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
									<details class="file">
										<summary>
											<code>{$item.file}:{$item.line}</code>
										</summary>
										{assign var="file" value=$values.cache[$item.file]}
										<pre class="source" data-line="{$item.line}"><code class="language-php line-numbers">{$file}</code></pre>
									</details>
									<table>
										{if isset($item.object) }
											<tr>
												<td>object</dt>
												<td><code>{$item.object}</code></dt>
											</tr>
										{/if}
										<tr>
											<td>function</td>
											<td>
												{if isset($item.class) }
													<code title="class">{$item.class}</code>
												{/if}
												<code title="type">{$item.type}</code>
												<code>{$item.function}</code>
											</td>
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

		{if PeServer\Core\Environment::isDevelopment() && !is_null($values.throwable)}
			{asset file='./prism.js' include='true'}
			<script>

			function scrollHighlight(sourceElement) {
				const lines = sourceElement.querySelector('.line-highlight');
				const linesHeight = lines.offsetHeight;
				const sourceHeight = sourceElement.offsetHeight;
				//lines.scrollIntoView();
				if (sourceHeight > linesHeight && sourceElement.scrollTop < (sourceElement.scrollHeight - sourceHeight)) {
					//sourceElement.scrollTop = sourceElement.scrollTop - (sourceHeight / 2) + (linesHeight / 2);
					const top = sourceElement.scrollTop - (sourceHeight / 2); //+ (linesHeight / 2);
					{literal}
						sourceElement.scroll({top: top});
					{/literal}
				}
			}

			window.addEventListener('load', (event) => {
				const sourceElement = document.getElementById('throw');
				scrollHighlight(sourceElement);
				{literal}
					window.scroll({top: 0});
				{/literal}

				const fileElements = document.querySelectorAll('.file');
				for(const fileElement of fileElements) {
					fileElement.addEventListener('toggle', (toggleEvent) => {
						const target = toggleEvent.target;
						if(target.open) {
							scrollHighlight(target);
						}
					})
				}
			});
		{/if}

		</script>
	</body>
</html>
