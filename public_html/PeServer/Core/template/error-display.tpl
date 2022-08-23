{$show_exception = PeServer\Core\Environment::isDevelopment() && !is_null($values.throwable)}
<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8"/>
		<title>Pe.Server.Core: Error</title>
		{asset file='./error-display.css' include='true'}
		{if PeServer\Core\Environment::isDevelopment() && !is_null($values.throwable)}
			{asset file='./highlight.php/default.css' include='true'}
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

				{if isset($values.file)}
				<dt>ソース</dt>
				<dd>
				<code>{$values.file}</code>:<code>{$values.line_number}</code>
					{if PeServer\Core\Environment::isDevelopment() && !is_null($values.throwable)}
						{assign var="file" value=$values.cache[$values.file]}
						{code language="php" numbers=$values.line_number}{$file nofilter}{/code}
					{/if}
				</dd>
				{/if}

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
											{if isset($item.file)}
												<code>{$item.file}:{$item.line}</code>
											{else}
												<code>しらん</code>
											{/if}
										</summary>
										{if isset($item.file)}
											{assign var="file" value=$values.cache[$item.file]}
											{code language="php" numbers=$item.line}{$file nofilter}{/code}
										{/if}
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
												{if isset($item.type) }
													<code title="type">{$item.type}</code>
												{/if}
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
																<details>
																	<summary>[{$key}] <code>{\PeServer\Core\TypeUtility::getType($values)}</code></summary>
																	<pre class="argument">{$values|@var_dump}</pre>
																</details>
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
			<script>

			function scrollHighlight(sourceElement) {
				const codeElement = sourceElement.querySelector('code');
				const strongLineElement = codeElement.querySelector('.strong-line');
				const strongOffsetTop = strongLineElement.offsetTop;
				const strongOffsetHeight = strongLineElement.offsetHeight;

				sourceElement.scrollTop = strongOffsetTop;// - strongOffsetHeight;

			}

			window.addEventListener('load', (event) => {
				const sourceElement = document.querySelector('pre.source');
				scrollHighlight(sourceElement);
				{literal}
					window.scroll({top: 0});
				{/literal}

				const fileElements = document.querySelectorAll('.file');
				for(const fileElement of fileElements) {
					fileElement.addEventListener('toggle', (toggleEvent) => {
						const target = toggleEvent.target;
						if(target.open) {
							const element = target.querySelector('pre.source');
							scrollHighlight(element);
						}
					})
				}
			});
		{/if}

		</script>
	</body>
</html>
