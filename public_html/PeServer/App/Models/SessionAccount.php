<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use \Serializable;
use PeServer\App\Models\Domain\UserLevel;
use PeServer\App\Models\Domain\UserState;

/**
 * @immutable
 */
class SessionAccount implements Serializable
{
	/**
	 * 生成。
	 *
	 * @param string $userId
	 * @param string $loginId
	 * @param string $name
	 * @param string $level
	 * @phpstan-param UserLevel::* $level
	 * @param string $state
	 * @phpstan-param UserState::* $state
	 */
	public function __construct(
		public string $userId,
		public string $loginId,
		public string $name,
		public string $level,
		public string $state
	) {
	}

	//Serializable

	public function serialize(): string
	{
		return serialize([
			'user_id' => $this->userId,
			'login_id' => $this->loginId,
			'name' => $this->name,
			'level' => $this->level,
			'state' => $this->state,
		]);
	}

	public function unserialize(string $data): void
	{
		$values = unserialize($data);
		$this->userId = $values['user_id']; //@phpstan-ignore-line Serializable
		$this->loginId = $values['login_id']; //@phpstan-ignore-line Serializable
		$this->name = $values['name']; //@phpstan-ignore-line Serializable
		$this->level = $values['level']; //@phpstan-ignore-line Serializable
		$this->state = $values['state']; //@phpstan-ignore-line Serializable
	}
}
