{extends file='default.tpl'}
{block name='TITLE'}ユーザー情報 編集{/block}
{block name='BODY'}

<form class="page-account-user" action="/account/user/edit" method="post">
	{csrf}

	<dl class="input">
		<dt>名前</dt>
		<dd>
			{input_helper key='account_edit_name' type="text" class="edit" required="true"}
		</dd>

		<dt>Webサイト</dt>
		<dd>
			{input_helper key='account_edit_website' type="url" class="edit" required="true"}
		</dd>

		<dt>説明</dt>
		<dd>
			<div class="tab">
				<input id="description_markdown_source" type="radio" name="tab_markdown" class="tab_check" checked><label for="description_markdown_source" class="tab_header">Markdown</label>
				<div class="tab_content">{input_helper key='account_edit_description' type="textarea" class="edit markdown-editor" data-markdown-result=".markdown-browser" required="true"}</div>

				<input id="description_markdown_preview" type="radio" name="tab_markdown" class="tab_check"><label for="description_markdown_preview" class="tab_header">プレビュー</label>
				<div class="tab_content">{markdown class="markdown markdown-browser"}{$values.account_edit_description}{/markdown}</div>
			</div>

		</dd>

		<dt class="action"></dt>
		<dd class="action">
			<button>登録</button>
		</dd>
	</dl>
</form>

{/block}

{block name='DEFAULT_SCRIPT'}{asset file='/scripts/user-edit.js'}{/block}
