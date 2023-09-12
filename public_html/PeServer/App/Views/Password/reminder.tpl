{extends file='default.tpl'}
{block name='TITLE'}パスワード再設定{/block}
{block name='BODY'}

	<form action="/password/reminder" method="post">
		{csrf}

		<dl class="input">
			<dt>ログインID</dt>
			<dd>
				{input_helper key='login_id' type="text" class="edit" required="true"}
			</dd>

			<dt class="action"></dt>
			<dd class="action">
				<button>再発行</button>
			</dd>
		</dl>
	</form>

{/block}
