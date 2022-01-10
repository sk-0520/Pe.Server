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
		<nav>
			<details>
				<summary>▼</summary>
				<div class="menu"></div>
			</details>
		</nav>
		<header>
			{include file='default.header.tpl'}
		</header>
		<footer>
			{include file='default.footer.tpl'}
		</footer>
		<div id="advertising">
			{if \PeServer\Core\Environment::isDevelopment() }
				<a><img alt="サーバー側の広告スペース" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAdQAAAA8BAMAAAAtaVYEAAAAMFBMVEUQAQYtb284eIsRAU/Dw4tvLQd+tsMBLU/Dw8PCqW+pby4ELW89fLUET4uLTwHCi0+UOF0zAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAC20lEQVRo3u2XsYrbQBCGFwxuLEMeIc1BnkC4i+BQeWAcSBqn3yp5iTWMVGgFV15z5EpzbdAjbKq0IkVikvLOYLhSTWZ2V/JKcXunSzIC21p7vd5v/pl/1gL+m0swKqMyKqMyKqMyKqMyKqMyKqMyKqMyKqMyKqMyKqM+D9R7fCTz4I28/ptQs6m/aeipwL0/9Cc0V93tXgsRAaglPR9Rt2o6WDSfPwfUrXtJTLurygt2RJXH2XvIzz6YQNXCDdb0TWEvqSIVgw2AEBP33Yu8WyOlOfGYqGrpAUrcyYyYTqL+hPzbjzZLr8V32jcO1TrIXodsfGgAtJABarvqeKiQzdzrHT5uraqSYOxVdbPXoOterTpVVRSgXsougTOSL/kaDxL4Uo6K6kOd4+bUiuQ7pSqql1WBxtrnaDZ3qAlNXYGKwzX3cDNA249Yqxu3l2wBh5e13enpWsVP1CJQVdeFOSfKiUNt3NROVXI0vbDxCy69GA9VvXNbTGsdqWWNzoTyhag7X37aftINd6+9qpszY1Hvj+5EKZ1F3vCwKoruXSjNeKiJT8obSVW0fGGo+BD1yqJuAz95BfCpG+3mraoPHz9T2Q6bTBL5bO35kBLjobamUbg8U3PrJ40zG4D3fT8JPKVNYLUmrQujK8r9hOSbWFFRP5e8q+AXyy/RWKjaNz/1xqdxZWVo7A1W1rTvJxvzhy0VFZQSCx7tGKvVehMF5GDQrlLZa9u4XgTlSM0mbfNp5zagSWOsz0a59nMwfT8JTIWajXQNSsdIh3ypgXOL6pKkLQ11FPKXBL/yU6Nee01z3z0LGmPkoXHohyDbnJ/cDftqUtuI3GI/VmLaqmobUAfVnSatokk14hm4nIWnVUrSQ2wDEPaJdf8MSVFAVHtOwLPz25KyeetrVbqIpAMLKirfn/+JP3FtAvP/VUZlVEZlVEZlVEZlVEZlVEZlVEZlVEZlVEZlVEZ9vOs3P+f4VYm0nHUAAAAASUVORK5CYII=" /></a>
			{else}
				<script type="text/javascript" src="https://cache1.value-domain.com/xa.j?site=peserver.s203.xrea.com"></script>
			{/if}
		</div>
		{include file='default.foot.tpl'}
	</body>
</html>
