{extends file='default.tpl'}
{block name=TITLE}ログイン{/block}
{block name=BODY}

<form class="page-login" action="login" method="post">
	<dl class="input">
		<dt>id</dt>
		<dd><input type="text" value="" /></dd>

		<dt>password</dt>
		<dd><input type="password" value="" /></dd>

		<dl class="action"></dl>
		<dd>
			<button>submit</button>
		</dd>
	</dl>
</form>

{/block}
