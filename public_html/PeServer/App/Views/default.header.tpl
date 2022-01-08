<ul>
	<li>
		<a href="/">top</a>
	</li>
	{if isset($smarty.session[constant('PeServer\\App\\Models\\SessionManager::ACCOUNT')])}
		{if $smarty.session[constant('PeServer\\App\\Models\\SessionManager::ACCOUNT')]['level'] == constant('PeServer\\App\\Models\\Domains\\UserLevel::SETUP')}
			<li>
				<a href="/setting/setup">セットアップ</a>
			</li>
		{else}
			<li>
				<a href="/account/user">ユーザー情報</a>
			</li>
		{/if}
		{if $smarty.session[constant('PeServer\\App\\Models\\SessionManager::ACCOUNT')]['level'] == constant('PeServer\\App\\Models\\Domains\\UserLevel::ADMINISTRATOR')}
			<li>
				<a href="/setting">設定</a>
			</li>
		{/if}
		<li>
			<a href="/account/logout">logout</a>
		</li>
	{else}
		<li>
			<a href="/account/login">login</a>
		</li>
		<li>
			<a href="/account/signup">signup</a>
		</li>
	{/if}
</ul>
