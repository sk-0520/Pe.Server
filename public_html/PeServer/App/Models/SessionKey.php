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
	 * 一時的なセッション。
	 *
	 * 以下の画面でCSRFを使用するためにトークンが突っ込まれるイメージ
	 * * ログイン画面
	 * * パスワード再発行
	 * * 新規登録
	 */
	public const TEMPORARY = 'temporary';

	#endregion
}
