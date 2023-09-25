<?php

declare(strict_types=1);

namespace PeServer\App\Models\Data\Dto;

use DateTime;
use PeServer\App\Models\Domain\UserLevel;
use PeServer\App\Models\Domain\UserState;
use PeServer\Core\Database\DtoBase;
use PeServer\Core\Serialization\Mapping;
use PeServer\Core\Text;

/**
 * ログインユーザー情報。
 *
 * @immutable
 */
class LoginUserDto extends DtoBase
{
	#region variable

	#[Mapping('user_id')]
	public string $userId = Text::EMPTY;

	#[Mapping('login_id')]
	public string $loginId = Text::EMPTY;

	public string $name = Text::EMPTY;

	/**
	 * @phpstan-var UserLevel::*
	 */
	public string $level = UserLevel::UNKNOWN;

	/**
	 * @phpstan-var UserState::*
	 */
	public string $state = UserState::UNKNOWN;

	#[Mapping('current_password')]
	public string $currentPassword = Text::EMPTY;

	#endregion
}
