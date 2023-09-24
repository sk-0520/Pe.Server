{extends file='default.tpl'}
{block name='TITLE'}パスワード再発行最終工程{/block}
{block name='BODY'}

	<form class="page-password-reset" action="/password/reset/{$values.token}" method="post">
		{csrf}

		<dl class="input">
			<dt>ログインID</dt>
			<dd>
				{input_helper key='reminder_login_id' type="text" class="edit" required="true"}
			</dd>

			<dt>新規パスワード</dt>
			<dd>
				{input_helper key='reminder_password_new' type="password" class="edit" required="true"}
			</dd>

			<dt>新規パスワード(確認用)</dt>
			<dd>
				{input_helper key='reminder_password_confirm' type="password" class="edit" required="true"}
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
