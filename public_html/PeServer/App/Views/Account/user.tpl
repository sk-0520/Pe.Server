{extends file='default.tpl'}
{block name=TITLE}ユーザー情報{/block}
{block name=BODY}

<dl class="page-account-user">
	<dt>user id</dt>
	<dd>{$smarty.session[constant('PeServer\\App\\Models\\SessionKey::ACCOUNT')]['id']}</dd>

	<dt>login id</dt>
	<dd>{$smarty.session[constant('PeServer\\App\\Models\\SessionKey::ACCOUNT')]['login_id']}</dd>

	<dt>level</dt>
	<dd>{$smarty.session[constant('PeServer\\App\\Models\\SessionKey::ACCOUNT')]['level']}</dd>

	<dt>user name</dt>
	<dd>{$smarty.session[constant('PeServer\\App\\Models\\SessionKey::ACCOUNT')]['name']}</dd>

	<dt>email</dt>
	<dd></dd>

	<dt>website</dt>
	<dd></dd>
</dl>

{/block}
