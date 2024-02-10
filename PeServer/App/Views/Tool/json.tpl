{extends file='default.tpl'}
{block name='TITLE'}ツール/Json{/block}
{block name='BODY'}

	<p>
		<strong>本処理はサーバーを経由する。</strong>
	</p>

	<form action="/tool/json" method="post">
		{csrf}

		<dl class="input">
			<dt>input</dt>
			<dd>
				{input_helper key='tool_json_input' type="textarea"}
			</dd>

			<dt>type</dt>
			<dd>
				<ul class="inline">
					<li>
						<label>
							<input type="radio" name="tool_json_kind" value="format" {if $values.tool_json_kind === 'format'}checked{/if}>
							整形
						</label>
					</li>
					<li>
						<label>
							<input type="radio" name="tool_json_kind" value="minify" {if $values.tool_json_kind === 'minify'}checked{/if}>
							ちっちゃくする
						</label>
					</li>
				</ul>
				{show_error_messages key="tool_json_kind"}
			</dd>

			<dt class="action"></dt>
			<dd class="action">
				<button type="submit">submit</button>
			</dd>
		</dl>
	</form>

	{if !empty($values.has_result)}
		<pre wrap="soft" data-clipboard="block">{$values.tool_json_result}</pre>
	{/if}

{/block}
