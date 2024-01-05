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
 * ユーザー情報。
 */
class UserInformationDto extends DtoBase
{
	#region variable

	#[Mapping('user_id')]
	public string $userId = Text::EMPTY;

	#[Mapping('login_id')]
	public string $loginId = Text::EMPTY;

	/**
	 * @phpstan-var UserLevel::*
	 */
	public string $level = UserLevel::UNKNOWN;

	public string $name = Text::EMPTY;

	public string $email = Text::EMPTY;

	public string $website = Text::EMPTY;

	#endregion
}
