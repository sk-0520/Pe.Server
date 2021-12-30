<!DOCTYPE html>
<html lang="ja">
	<head>
		{include file='default.head.tpl'}
	</head>
	<body>
		<main id="main">
			<h1>{block name='TITLE'}{/block}</h1>
			{show_error_messages}
			<section id="content">
				{block name='BODY'}{/block}
			</section>
		</main>
		<header>
			{include file='default.header.tpl'}
		</header>
		<footer>
			{include file='default.footer.tpl'}
		</footer>
		{include file='default.foot.tpl'}
	</body>
</html>
