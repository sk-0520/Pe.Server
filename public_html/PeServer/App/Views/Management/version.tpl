{extends file='default.tpl'}
{block name='TITLE'}バージョン設定{/block}
{block name='BODY'}

	<form action="/management/version" method="post">
		{csrf}

		<dl>
			<dt>バージョン</dt>
			<dd>
				{input_helper key='version' type="text" class="edit"}
			</dd>

			<dt class="action"></dt>
			<dd class="action">
				<button>登録</button>
			</dd>
		</dl>

	</form>

{/block}
