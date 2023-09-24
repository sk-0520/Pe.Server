<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page;

use PeServer\App\Models\Data\SessionAnonymous;
use PeServer\App\Models\SessionKey;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Throws\HttpStatusException;

trait SessionAnonymousTrait
{
	#region function

	private function isTrueProperty(string $propertyName): bool
	{
		if ($this->existsSession(SessionKey::ANONYMOUS)) {
			$sessionAnonymous = $this->getSession(SessionKey::ANONYMOUS);
			if ($sessionAnonymous !== null && $sessionAnonymous instanceof SessionAnonymous) {
				/** @var SessionAnonymous $sessionAnonymous */
				if ($sessionAnonymous->$propertyName) {
					return true;
				}
			}
		}

		return false;
	}

	protected function isEnabledLogin(): bool
	{
		return $this->isTrueProperty('login');
	}

	protected function isEnabledSignup1(): bool
	{
		return $this->isTrueProperty('signup1');
	}

	protected function isEnabledSignup2(): bool
	{
		return $this->isTrueProperty('signup2');
	}

	protected function isPasswordReminder(): bool
	{
		return $this->isTrueProperty('passwordReminder');
	}

	protected function isPasswordReset(): bool
	{
		return $this->isTrueProperty('passwordReset');
	}

	private function throwHttpStatusIfIsDisabled(string $propertyName, HttpStatus $httpStatus): void
	{
		if (!$this->isTrueProperty($propertyName)) {
			throw new HttpStatusException($httpStatus);
		}
	}

	protected function throwHttpStatusIfNotLogin(HttpStatus $httpStatus): void
	{
		$this->throwHttpStatusIfIsDisabled('login', $httpStatus);
	}

	protected function throwHttpStatusIfNotSignup1(HttpStatus $httpStatus): void
	{
		$this->throwHttpStatusIfIsDisabled('signup1', $httpStatus);
	}

	protected function throwHttpStatusIfNotSignup2(HttpStatus $httpStatus): void
	{
		$this->throwHttpStatusIfIsDisabled('signup2', $httpStatus);
	}

	protected function throwHttpStatusIfNotPasswordReminder(HttpStatus $httpStatus): void
	{
		$this->throwHttpStatusIfIsDisabled('passwordReminder', $httpStatus);
	}

	protected function throwHttpStatusIfNotPasswordReset(HttpStatus $httpStatus): void
	{
		$this->throwHttpStatusIfIsDisabled('passwordReset', $httpStatus);
	}

	#endregion
}
