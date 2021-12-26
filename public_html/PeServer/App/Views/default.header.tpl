<ul>
	<li>
		<a href="/">top</a>
	</li>
	{if isset($smarty.session[constant('PeServer\\App\\Models\\SessionKey::ACCOUNT')])}
		{if $smarty.session[constant('PeServer\\App\\Models\\SessionKey::ACCOUNT')]['level'] == constant('PeServer\\App\\Models\\UserLevel::SETUP')}
			<li>
				<a href="/setting/setup">セットアップ</a>
			</li>
		{else}
			<li>
				<a href="/account/user">ユーザー情報</a>
			</li>
		{/if}
		{if $smarty.session[constant('PeServer\\App\\Models\\SessionKey::ACCOUNT')]['level'] == constant('PeServer\\App\\Models\\UserLevel::ADMINISTRATOR')}
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
	{/if}
</ul>
