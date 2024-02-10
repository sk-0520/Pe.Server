<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8"/>
		<title>{$values.app.subject} - メール</title>
		{asset file='email.css' include='true'}
		{block name='STYLES'}{/block}
	</head>
	<body>
		<h1>{$values.app.subject}</h1>
		{include file='email.header.tpl'}
		<div class="content">
			{block name='BODY'}{/block}
		</div>
		{include file='email.footer.tpl'}
	</body>
</html>
