{extends file='default.tpl'}
{block name=TITLE}セットアップ{/block}
{block name=BODY}

<p>
	セットアップユーザーから管理者を生成するための情報を入力。<br />
	管理者作成が完了した時点で元セットアップユーザーは無効化される。
</p>
<p>
	このセットアップ処理は管理者がセットアップユーザーとして処理している想定のため検証を甘くしている。<br />
	そのため、ここで設定したユーザー情報でも設定画面で無編集保存ができない可能性あり。
</p>

<form class="page-setting-setup" action="/setting/setup" method="post">
	<dl class="input">
		<dt>login id</dt>
		<dd>
			{input_helper key='setting_setup_login_id' type="text" class="edit"}
		</dd>

		<dt>password</dt>
		<dd>
			{input_helper key='setting_setup_password' type="text" class="edit"}
		</dd>

		<dt>user name</dt>
		<dd>
			{input_helper key='setting_setup_user_name' type="text" class="edit"}
		</dd>

		<dt>web site</dt>
		<dd>
			{input_helper key='setting_setup_web_site' type="text" class="edit"}
		</dd>

		<dt>mail address</dt>
		<dd>
			{input_helper key='setting_setup_mail_address' type="text" class="edit"}
		</dd>

		<dl class="action"></dl>
		<dd class="action">
			<button>submit</button>
		</dd>
	</dl>
</form>

{/block}
