<!DOCTYPE html>
<html lang="ja">
	<head>
		{include file='default.head.tpl'}
	</head>
	<body>
		<main id="main">
			<h1>{block name=TITLE}{/block}</h1>
			{if count($errors)}
				<ul class="common-error errors">
					{foreach from=$errors item=item}
						{foreach from=$item item=error}
							<li class="error">{$error}</li>
						{/foreach}
					{/foreach}
				</ul>
			{/if}
			<section id="content">
				{block name=BODY}{/block}
			</section>
		</main>
		<header>
			{include file='default.header.tpl'}
		</header>
		<footer>
			{include file='default.footer.tpl'}
		</footer>
	</body>
	{include file='default.foot.tpl'}
</html>
