<?php

declare(strict_types=1);

namespace PeServer\App\Models;

class AuditLog
{
	public const LOGIN_SUCCESS = 'LOGIN-SUCCESS';
	public const LOGIN_FAILED = 'LOGIN-FAILED';
	public const LOGOUT = 'LOGOUT';

	public const USER_CREATE = 'USER-CREATE';
	public const USER_GENERATED = 'USER-GENERATED';
	public const USER_STATE_CHANGE = 'USER-STATE-CHANGE';
	public const USER_EDIT = 'USER-EDIT';
}
