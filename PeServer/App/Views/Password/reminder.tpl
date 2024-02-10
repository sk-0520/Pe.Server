{extends file='default.tpl'}
{block name='TITLE'}パスワード再設定{/block}
{block name='BODY'}

	<form class="page-password-reminder" action="/password/reminder" method="post">
		{csrf}

		<dl class="input">
			<dt>ログインID</dt>
			<dd>
				{input_helper key='reminder_login_id' type="text" class="edit" required="true"}
			</dd>

			<dt class="action"></dt>
			<dd class="action">
				<button>再発行</button>
			</dd>
			<dd>
				{include file="template/page/email-info.tpl" email=$values.email}
			</dd>
		</dl>
	</form>

{/block}
