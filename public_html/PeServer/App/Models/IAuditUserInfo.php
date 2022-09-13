<?php

declare(strict_types=1);

namespace PeServer\App\Models;

/**
 * 監査用ユーザー情報取得。
 */
interface IAuditUserInfo
{
	#region function

	/**
	 * ユーザーID取得。
	 *
	 * @return string
	 */
	function getUserId(): string;

	#endregion
}
