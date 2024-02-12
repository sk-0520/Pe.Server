{extends file='default.tpl'}
{block name='TITLE'}PHP実行{/block}
{block name='BODY'}

	<h2>入力</h2>
	<form class="page-setting-php-evaluate" action="/management/php-evaluate" method="post">
		{csrf}

		<dl class="input">
			<dt>PHP</dt>
			<dd>
				{input_helper key='php_statement' type="textarea" class="edit" required="true"}
			</dd>

			<dt class="action">実行</dt>
			<dd class="action">
				<button>submit</button>
			</dd>
		</dl>

	</form>

	{if $values.executed}
		<h2>結果</h2>
		<dl>
			<dt>出力</dt>
			<dd>
				{if ($values.output instanceof \Stringable) || is_string($values.output) }
					<pre data-clipboard="block">{$values.output}</pre>
				{else}
					<pre data-clipboard="block">{$values.output|dump}</pre>
				{/if}
			</dd>

			<dt>結果</dt>
			<dd>
				{if ($values.result instanceof \Stringable) || is_string($values.result) }
					<pre data-clipboard="block">{$values.result}</pre>
				{elseif $values.result}
					<pre data-clipboard="block">{$values.result|dump}</pre>
				{else}
					なし
				{/if}
			</dd>

			<dt>実行PHPソース</dt>
			<dd>
				<pre data-clipboard="block">{$values.execute_statement}</pre>
			</dd>
		</dl>

	{/if}

{/block}
