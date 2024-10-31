<!DOCTYPE html>
<html lang="ja">
	<!--nobanner-->

	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width,initial-scale=1.0" />
		<title>{block name='TITLE'}{/block} - Peサーバー</title>
		{block name='HEAD'}{/block}
		{asset file='/favicon.svg' rel="icon" type="image/svg+xml"}
		{block name='DEFAULT_STYLE'}{asset file='/styles/style.css'}{/block}
		{block name='STYLES'}{/block}
		{if !$environment->isDevelopment() }
			<script>
				var _paq = window._paq = window._paq || [];
				/* tracker methods like "setCustomDimension" should be called before "trackPageView" */
				_paq.push(['trackPageView']);
				_paq.push(['enableLinkTracking']);
				(function() {
					var u = "//analytics.content-type-text.org/";
					_paq.push(['setTrackerUrl', u + 'matomo.php']);
					_paq.push(['setSiteId', '3']);
					var d = document,
						g = d.createElement('script'),
						s = d.getElementsByTagName('script')[0];
					g.async = true;
					g.src = u + 'matomo.js';
					s.parentNode.insertBefore(g, s);
				})();
			</script>
		{/if}
	</head>

	<body>
		<main id="main">
			<h1 id="title">
				{block name='TITLE'}{/block}
				{if $environment->isDevelopment()}
					<span class="mute" style="margin-left: 1em;">(dev)</span>
				{/if}
			</h1>
			{if isset($values.temp_messages) && !empty($values.temp_messages)}
				<div class="common messages">
					<ul>
						{foreach from=$values.temp_messages item=item key=key}
							<li data-index="{$key}">{$item}</li>
						{/foreach}
					</ul>
				</div>
			{/if}
			{show_error_messages}
			<section id="content">
				{block name='BODY'}{/block}
			</section>
		</main>
		<nav>
			<details>
				<summary></summary>
				<div class="menu"></div>
			</details>
		</nav>
		<header>
			<ul>
				<li>
					<a href="/">トップ</a>
				</li>
				{assign var="account" value=$stores.session.account}
				{if $account}
					{if $account->level == 'setup'}
						<li>
							<a href="/management/setup">セットアップ</a>
						</li>
					{else}
						<li>
							<a href="/account/user">ユーザー情報</a>
						</li>
					{/if}
					{if $account->level == 'administrator'}
						<li>
							<a href="/management">管理</a>
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
		</header>
		<footer>
			<ul>
				<li>
					<a href="/">トップページ</a>
				</li>
				<li>
					<a href="/about">このサイトについて</a>
				</li>
			</ul>
		</footer>
		{block name='DEFAULT_SCRIPT'}{asset file='/scripts/script.js'}{/block}
		{block name='SCRIPTS'}{/block}
		{if $environment->isDevelopment() }
			<script>
				{* DOMContentLoaded やら普通にぶっこむ系だと通常表示におけるブラウザ待機時間が目に付くので load でいいのです *}
				window.addEventListener('load', (event) => {
					const script = document.createElement('script');
					script.src = 'http://localhost:35729/livereload.js';
					document.body.appendChild(script);
				});
			</script>
		{/if}
	</body>

</html>
