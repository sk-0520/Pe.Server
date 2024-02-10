{extends file='default.tpl'}
{block name='TITLE'}ツール/base64{/block}
{block name='BODY'}

	<p>
		<strong>本処理はサーバーを経由する。</strong>
	</p>

	<form action="/tool/base64" method="post">
		{csrf}

		<dl class="input">
			<dt>input</dt>
			<dd>
				{input_helper key='tool_base64_input' type="textarea"}
			</dd>

			<dt>type</dt>
			<dd>
				<ul class="inline">
					<li>
						<label>
							<input type="radio" name="tool_base64_kind" value="encode" {if $values.tool_base64_kind === 'encode'}checked{/if}>
							エンコード(あ => 44GC)
						</label>
					</li>
					<li>
						<label>
							<input type="radio" name="tool_base64_kind" value="decode" {if $values.tool_base64_kind === 'decode'}checked{/if}>
							デコード(44GC => あ)
						</label>
					</li>
				</ul>
				{show_error_messages key="tool_base64_kind"}
			</dd>

			<dt class="action"></dt>
			<dd class="action">
				<button type="submit">submit</button>
			</dd>
		</dl>
	</form>

	{if !empty($values.has_result)}
		<pre wrap="soft" data-clipboard="block">{$values.tool_base64_result}</pre>
	{/if}

{/block}
