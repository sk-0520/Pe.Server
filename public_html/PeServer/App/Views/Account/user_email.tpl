{extends file='default.tpl'}
{block name='TITLE'}メールアドレス変更{/block}
{block name='BODY'}

	<section class="page-account-email-edit">
		<h2>変更前処理</h2>

		<p>
			変更先メールアドレスに変更用トークンを送信します。
		</p>

		<form action="/account/user/email" method="post">
			{csrf}
			<input type="hidden" name="account_email_mode" value="edit" />

			<dl class="input">
				<dt>変更先メールアドレス</dt>
				<dd>
					{input_helper key='account_email_email' type="email" class="edit" required="true"}
				</dd>

				<dt class="action"></dt>
				<dd class="action">
					<button>送信</button>
				</dd>
			</dl>
		</form>
	</section>

	{if $values.wait_email }
		<section class="page-account-email-confirm">
			<h2>変更後処理</h2>

			<p>
				変更用トークン入力待ちです。
			</p>

			<form action="/account/user/email" method="post">
				{csrf}
				<input type="hidden" name="account_email_mode" value="confirm" />

				<dl class="input">
					<dt>待機中メールアドレス</dt>
					<dd>
						<code data-clipboard="inline">{$values.wait_email}</code>
					</dd>

					<dt>発行日時</dt>
					<dd>
						<time datetime="{$values.token_timestamp_utc}">{$values.token_timestamp_utc}</time>
					</dd>

					<dt>変更用トークン</dt>
					<dd>
						{input_helper key='account_email_token' type="text" class="edit" required="true"}
					</dd>

					<dt class="action"></dt>
					<dd class="action">
						<button>変更</button>
					</dd>
				</dl>
			</form>

		</section>
	{/if}

{/block}
