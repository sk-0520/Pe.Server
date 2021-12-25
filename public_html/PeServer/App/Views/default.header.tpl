<ul>
	<li>
		<a href="/">top</a>
	</li>
	<li>
		{if isset($smarty.session.user)}
			<a href="/account/logout">logout</a>
		{else}
			<a href="/account/login">login</a>
		{/if}
	</li>
</ul>
