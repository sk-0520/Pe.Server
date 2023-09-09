<?php

declare(strict_types=1);

namespace PeServer\App\Models\Data\Dto;

use PeServer\App\Models\Domain\UserLevel;
use PeServer\App\Models\Domain\UserState;
use PeServer\Core\Database\DtoBase;
use PeServer\Core\Serialization\Mapping;
use PeServer\Core\Text;

/**
 * @immutable
 */
class UserListItemDto extends DtoBase
{
	#region variable

	#[Mapping(name: 'user_id')]
	public string $userId = Text::EMPTY;

	#[Mapping(name: 'login_id')]
	public string $loginId = Text::EMPTY;

	/**
	 * ユーザーレベル。
	 *
	 * @phpstan-var UserLevel::*
	 */
	public string $level = UserLevel::UNKNOWN;
	/**
	 * ユーザー状態
	 *
	 * @phpstan-var UserState::*
	 */
	public string $state = UserState::UNKNOWN;

	/**
	 * 名前。
	 */
	public string $name = Text::EMPTY;


	#endregion
}
