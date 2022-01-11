<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain;

abstract class UserLevel
{
	public const USER = 'user';
	public const SETUP = 'setup';
	public const ADMINISTRATOR = 'administrator';
}
