<!DOCTYPE html>
<html lang="ja">
	<head>
		{include file='default.head.tpl'}
	</head>
	<body>
		<header>
			{include file='default.header.tpl'}
		</header>
		<main id="main">
			<h1>{block name=TITLE}{/block}</h1>
			<section id="content">
				{block name=BODY}{/block}
			</section>
		</main>
		<footer>
			{include file='default.footer.tpl'}
		</footer>
	</body>
	{include file='default.foot.tpl'}
</html>
