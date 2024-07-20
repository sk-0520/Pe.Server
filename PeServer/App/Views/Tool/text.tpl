{extends file='default.tpl'}
{block name='TITLE'}ツール/テキスト{/block}
{block name='DEFAULT_SCRIPT'}{asset file='/scripts/tool_text.js'}{/block}
{block name='BODY'}

	<p>
		<strong>本処理はサーバーを経由しない。</strong>
	</p>

	<section>
		<h2>生成</h2>
		<dl class="input">
			<dt>input</dt>
			<dd>
				<ul>
					<li>
						<label>
							<input id="generator-input-ascii-number-enable" type="checkbox" />
							ASCII 数字
						</label>
					</li>
					<li>
						<label>
							<input id="generator-input-ascii-alphabet-upper-enable" type="checkbox" />
							ASCII アルファベット大文字
						</label>
					</li>
					<li>
						<label>
							<input id="generator-input-ascii-alphabet-lower-enable" type="checkbox" />
							ASCII アルファベット小文字
						</label>
					</li>
					<li>
						<label>
							<input id="generator-input-ascii-graphic-enable" type="checkbox" />
							ASCII 記号
						</label>
					</li>
				</ul>
			</dd>

			<dt class="action"></dt>
			<dd class="action">
				<button id="generator-submit" type="button">submit</button>
			</dd>

			<dt>output</dt>
			<dd>
				<pre id="generator-output" wrap="soft" data-clipboard="block"></pre>
			</dd>
		</dl>
	</section>


{/block}
