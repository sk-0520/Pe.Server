{extends file='default.tpl'}
{block name='TITLE'}ユーザー情報 編集{/block}
{block name='BODY'}

[{$values.wait_email}]
[{$values.token_timestamp}]

<section class="page-account-email-edit">
	<h2>アドレス変更</h2>

	<form action="/account/user/email" method="post">
		{csrf}
		<input type="hidden" name="account_email_mode" value="edit" />

		<dl>
			<dt>mail address</dt>
			<dd>
				{input_helper key='account_email_email' type="text" class="edit"}
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
		<form action="/account/user/email" method="post">
			{csrf}
			<input type="hidden" name="account_email_mode" value="confirm" />

			<dl>
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
