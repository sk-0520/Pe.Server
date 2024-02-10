<?php

declare(strict_types=1);

namespace PeServer\App\Models;

abstract class SessionKey
{
	#region function

	/**
	 * アカウント情報。
	 *
	 * ログイン済みの状態。
	 */
	public const ACCOUNT = 'account';
	/**
	 * 一時的な匿名セッション。
	 *
	 * 以下の画面でCSRFを有効にするために使う
	 *
	 * * ログイン画面
	 * * パスワード再発行
	 * * 新規登録
	 */
	public const ANONYMOUS = 'anonymous';

	#endregion
}
