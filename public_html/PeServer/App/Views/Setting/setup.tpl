{extends file='default.tpl'}
{block name=TITLE}セットアップ{/block}
{block name=BODY}

<p>
	セットアップユーザーから管理者を生成するための情報を入力。<br />
	管理者作成が完了した時点で元セットアップユーザーは無効化される。
</p>
<p>
	このセットアップ処理は管理者がセットアップユーザーとして処理している想定のため検証を甘くしている。
</p>

<form class="page-setting-setup" action="/setting/setup" method="post">
	<dl class="input">
		<dt>login id</dt>
		<dd>
			<input class="edit" name="setting_setup_login_id" type="text" value="{$values.setting_setup_login_id}" />
			{show_error_messages key='setting_setup_login_id'}
		</dd>

		<dt>password</dt>
		<dd>
			<input class="edit" name="setting_setup_password" type="text" value="" />
			{show_error_messages key='setting_setup_password'}
		</dd>

		<dt>user name</dt>
		<dd>
			<input class="edit" name="setting_setup_user_name" type="text" value="{$values.setting_setup_user_name}" />
			{show_error_messages key='setting_setup_user_name'}
		</dd>

		<dt>web site</dt>
		<dd>
			<input class="edit" name="setting_setup_web_site" type="text" value="{$values.setting_setup_web_site}" />
			{show_error_messages key='setting_setup_web_site'}
		</dd>

		<dt>mail address</dt>
		<dd>
			<input class="edit" name="setting_setup_mail_address" type="text" value="{$values.setting_setup_mail_address}" />
			{show_error_messages key='setting_setup_mail_address'}
		</dd>


		<dl class="action"></dl>
		<dd class="action">
			<button>submit</button>
		</dd>
	</dl>
</form>

{/block}

