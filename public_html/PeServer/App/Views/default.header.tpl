<ul>
	<li>
		<a href="/">トップ</a>
	</li>
	{if isset($smarty.session[constant('PeServer\\App\\Models\\SessionManager::ACCOUNT')])}
		{if $smarty.session[constant('PeServer\\App\\Models\\SessionManager::ACCOUNT')]['level'] == constant('PeServer\\App\\Models\\Domain\\UserLevel::SETUP')}
			<li>
				<a href="/setting/setup">セットアップ</a>
			</li>
		{else}
			<li>
				<a href="/account/user">ユーザー情報</a>
			</li>
		{/if}
		{if $smarty.session[constant('PeServer\\App\\Models\\SessionManager::ACCOUNT')]['level'] == constant('PeServer\\App\\Models\\Domain\\UserLevel::ADMINISTRATOR')}
			<li>
				<a href="/setting">設定</a>
			</li>
		{/if}
		<li>
			<a href="/account/logout">ログアウト</a>
		</li>
	{else}
		<li>
			<a href="/account/login">ログイン</a>
		</li>
		<li>
			<a href="/account/signup">ユーザー登録</a>
		</li>
	{/if}
</ul>
