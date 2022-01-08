{extends file='default.tpl'}
{block name='TITLE'}ユーザー情報 編集{/block}
{block name='BODY'}

<form class="page-account-signup" action="/account/signup" method="post">
	<dl>
		<dt>email</dt>
		<dd>
			{input_helper key='account_signup_email' type="email" class="edit"}
		</dd>

		<dt class="action">action</dt>
		<dd class="action">
			<button>submit</button>
		</dd>
	</dl>
</form>

{/block}
