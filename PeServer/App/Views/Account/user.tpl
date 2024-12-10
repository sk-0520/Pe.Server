{extends file='default.tpl'}
{block name='TITLE'}ユーザー情報{/block}
{block name='BODY'}

	<dl class="page-account-user">
		<dt>ユーザーID</dt>
		<dd>
			<code data-role="value" data-clipboard="inline">{$values.user->userId}</code>
		</dd>

		<dt>ログインID</dt>
		<dd>
			<code data-role="value" data-clipboard="inline">{$values.user->loginId}</code>
		</dd>

		<dt>権限</dt>
		<dd data-role="value">
			{PeServer\App\Models\Domain\UserLevel::toString($values.user->level)}
		</dd>

		<dt>名前</dt>
		<dd data-role="value">
			{$values.user->name}
		</dd>

		<dt>Webサイト</dt>
		<dd data-role="value">
			{if PeServer\Core\Text::isNullOrWhiteSpace($values.user->website)}
				<span class="mute">未登録</span>
			{else}
				<a href="{$values.user->website}" target="_blank" data-role="value">{$values.user->website}</a>
			{/if}
		</dd>

		<dt>プラグイン</dt>
		<dd data-role="value">
			{if empty($values.plugins)}
				<span class="mute">未登録</span>
			{else}
				<table>
					<thead>
						<tr>
							<th>状態</th>
							<th>プラグイン名</th>
							<th>プラグインID</th>
						</tr>
					</thead>
					<tbody>
						{foreach from=$values.plugins item=item key=key name=name}
							<tr>
								<td>
									{if $item.state === PeServer\App\Models\Domain\PluginState::ENABLED}
										[有効]
									{elseif $item.state === PeServer\App\Models\Domain\PluginState::CHECK_FAILED}
										[不明]
									{elseif $item.state === PeServer\App\Models\Domain\PluginState::RESERVED}
										[予約]
									{elseif $item.state === PeServer\App\Models\Domain\PluginState::DISABLED}
										[無効]
									{else}
										[あかん]
									{/if}
								</td>
								<td>
									{if $item.state === PeServer\App\Models\Domain\PluginState::ENABLED || $item.state === PeServer\App\Models\Domain\PluginState::RESERVED}
										<a href="/account/user/plugin/{$item.plugin_id}" title="{$item.display_name}">
											{$item.plugin_name}
										</a>
									{else}
										{$item.plugin_name}
									{/if}
								</td>
								<td data-clipboard="data" data-clipboard-value="{$item.plugin_id}">
									<code>{$item.plugin_id}</code>
								</td>
							</tr>
						{/foreach}
					</tbody>
				</table>
			{/if}
		</dd>

		<dt class="action">各種操作</dt>
		<dd class="action">
			<ul>
				<li>
					<a href="/account/user/plugin">プラグイン登録</a>
					<ul>
						<li>
							<a href="/account/user/plugin/reserve">予約</a>
						</li>
					</ul>
				</li>
				<li>
					<a href="/account/user/edit">ユーザー編集</a>
				</li>
				<li>
					<a href="/account/user/api">API設定</a>
				</li>
				<li>
					<a href="/account/user/email">メールアドレス変更</a>
				</li>
				<li>
					<a href="/account/user/password">パスワード変更</a>
				</li>
				<li>
					<a href="/account/user/audit-logs">監査ログ</a> (<a href="/account/user/audit-logs/download">DL</a>)
				</li>
			</ul>
		</dd>
	</dl>

{/block}
