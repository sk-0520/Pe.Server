{extends file='default.tpl'}
{block name='TITLE'}ユーザー情報 編集{/block}
{block name='BODY'}

<section class="page-account-email-edit">
	<h2>アドレス変更</h2>

	<form action="/account/user/email" method="post">
		{csrf}
		<input type="hidden" name="account_email_mode" value="edit" />

		<dl class="input">
			<dt>mail address</dt>
			<dd>
				{input_helper key='account_email_email' type="email" class="edit"}
			</dd>

			<dt class="action">edit</dt>
			<dd class="action">
				<button>submit</button>
			</dd>
		</dl>
	</form>
</section>

{if $values.wait_email }
	<section class="page-account-email-confirm">
		<p>
			メールアドレス確認待ちです。
		</p>

		<form action="/account/user/email" method="post">
			{csrf}
			<input type="hidden" name="account_email_mode" value="confirm" />

			<dl class="input">
				<dt>new email</dt>
				<dd>
					[{$values.wait_email}]
				</dd>

				<dt>timestamp</dt>
				<dd>
					<time datetime="{$values.token_timestamp_utc}">[{$values.token_timestamp_utc}]</time>
				</dd>

				<dt>token</dt>
				<dd>
					{input_helper key='account_email_token' type="text" class="edit"}
				</dd>

				<dt class="action">edit</dt>
				<dd class="action">
					<button>submit</button>
				</dd>
			</dl>
		</form>

	</section>
{/if}

{/block}
