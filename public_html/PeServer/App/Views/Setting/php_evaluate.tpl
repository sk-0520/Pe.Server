{extends file='default.tpl'}
{block name='TITLE'}PHP実行{/block}
{block name='BODY'}

<h2>入力</h2>
<form class="page-setting-php-evaluate" action="/setting/php-evaluate" method="post">
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
		<dt>output</dt>
		<dd>
			<pre data-clipboard="block">{$values.output}</pre>
		</dd>

		<dt>result</dt>
		<dd>
			<pre data-clipboard="block">{$values.result}</pre>
		</dd>

		<dt>execute_statement</dt>
		<dd>
			<pre data-clipboard="block">{$values.execute_statement}</pre>
		</dd>
	</dl>

{/if}

{/block}
