<!DOCTYPE html>
<html lang="ja">
	<!--nobanner-->
	<head>
		{include file='default.head.tpl'}
	</head>
	<body>
		<main id="main">
			<h1 id="title">{block name='TITLE'}{/block}</h1>
			{include file='default.message.tpl'}
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
		<div id="advertising">
			<script type="text/javascript" src="https://cache1.value-domain.com/xa.j?site=peserver.s203.xrea.com"></script>
		</div>
		{include file='default.foot.tpl'}
	</body>
</html>
