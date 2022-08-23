{extends file='default.tpl'}
{if $values.from_account_api_is_register}
	{block name='TITLE'}APIキー登録{/block}
	{$readonly = true}
{else}
	{block name='TITLE'}APIキー確認{/block}
	{$readonly = false}
{/if}
{block name='BODY'}

<form class="page-account-api" action="/account/user/api" method="post">
	{csrf}

	<dl class="input">
		<dt>説明</dt>
		<dd>
			<p>APIキーを用いてAPIを実行することができます。</p>
		</dd>

		<dt>APIキー</dt>
		{if $values.from_account_api_is_register}
			<dd>
				<p class="mute">APIキーは登録されていません。</p>
			</dd>
		{else}
			<dd>
				<table>
					<thead>
						<tr>
							<th>APIキー</th>
							<th>作成日</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td data-clipboard="inline"><code>{$values.api_key}</code></td>
							<td>{$values.created_timestamp}</td>
						</tr>
						{if $values.secret_key}
							<tr>
								<th colspan="2">シークレットキー</th>
							</tr>
							<tr>
								<td colspan="2" data-clipboard="inline"><code>{$values.secret_key}</code></td>
							</tr>
							<tr>
								<td colspan="2">
									<strong>シークレットキーは再表示できません。後生大事に保持しておいてください。</strong>
								</th>
							</tr>
						{/if}
					</tbody>
				</table>
			</dd>
		{/if}

		<dt class="action"></dt>
		<dd class="action">
			<button>
				{if $values.from_account_api_is_register}
					登録
				{else}
					削除
				{/if}
			</button>
		</dd>
	</dl>
</form>

{/block}
