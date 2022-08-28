<?php

declare(strict_types=1);

namespace PeServer\App\Models\Configuration;

use PeServer\App\Models\Configuration\PersistenceSetting;
use PeServer\Core\Serialization\Mapping;

/**
 * 確認設定。
 *
 * @immutable
 */
class ConfirmSetting
{
	#region variable

	#[Mapping(name: 'user_change_wait_email_minutes')]
	public int $userChangeWaitEmailMinutes;

	#[Mapping(name: 'sign_up_wait_email_minutes')]
	public int $signUpWaitEmailMinutes;

	#endregion
}
