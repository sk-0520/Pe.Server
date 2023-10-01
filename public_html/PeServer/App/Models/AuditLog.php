<?php

declare(strict_types=1);

namespace PeServer\App\Models;

class AuditLog
{
	#region define

	public const LOGIN_SUCCESS = 'LOGIN-SUCCESS';
	public const LOGIN_FAILED = 'LOGIN-FAILED';
	public const LOGOUT = 'LOGOUT';

	public const USER_CREATE = 'USER-CREATE';
	public const USER_GENERATED = 'USER-GENERATED';
	public const USER_STATE_CHANGE = 'USER-STATE-CHANGE';
	public const USER_EDIT = 'USER-EDIT';
	public const USER_PASSWORD_CHANGE = 'USER-PASSWORD-CHANGE';
	public const USER_EMAIL_CHANGING = 'USER-EMAIL-CHANGING';
	public const USER_EMAIL_CHANGED = 'USER-EMAIL-CHANGED';
	public const USER_PLUGIN_REGISTER = 'USER-PLUGIN-REGISTER';
	public const USER_PLUGIN_UPDATE = 'USER-PLUGIN-UPDATE';
	public const USER_API_KEY_REGISTER = 'USER-API-KEY-REGISTER';
	public const USER_API_KEY_UNREGISTER = 'USER-API-KEY-UNREGISTER';
	public const USER_PASSWORD_REMINDER_TOKEN = 'USER-PASSWORD-REMINDER-TOKEN';
	public const USER_PASSWORD_REMINDER_RESET = 'USER-PASSWORD-REMINDER-RESET';

	public const API_ADMINISTRATOR_BACKUP = 'API-ADMINISTRATOR-BACKUP';
	public const API_ADMINISTRATOR_VACUUM_ACCESS_LOG = 'API-ADMINISTRATOR-VACUUM-ACCESS_LOG';
	public const API_ADMINISTRATOR_DELETE_OLD_DATA = 'API-ADMINISTRATOR-DELETE-OLD_DATA';
	public const API_ADMINISTRATOR_CACHE_REBUILD = 'API-ADMINISTRATOR-CACHE-REBUILD';

	public const ADMINISTRATOR_DOWNLOAD_DATABASE = 'ADMINISTRATOR-DOWNLOAD-DATABASE';
	public const ADMINISTRATOR_SAVE_CONFIGURATION = 'ADMINISTRATOR-SAVE-CONFIGURATION';
	public const ADMINISTRATOR_EXECUTE_PHP = 'ADMINISTRATOR-EXECUTE-PHP';
	public const ADMINISTRATOR_EXECUTE_SQL = 'ADMINISTRATOR-EXECUTE-SQL';
	public const ADMINISTRATOR_SEND_EMAIL = 'ADMINISTRATOR-SEND-EMAIL';

	#endregion
}
