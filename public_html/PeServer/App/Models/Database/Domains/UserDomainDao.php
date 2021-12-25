<?php

declare(strict_types=1);

namespace PeServer\App\Models\Database\Domains;

use \PeServer\Core\DaoBase;
use \PeServer\Core\Database;

class UserDomainDao extends DaoBase
{
	public function __construct(Database $database)
	{
		parent::__construct($database);
	}

	/**
	 * Undocumented function
	 *
	 * @param array{account_login_login_id:string} $parameters
	 * @return array{user_id:string,level:string,password:string}|null
	 */
	public function selectUser(array $parameters)
	{
		return $this->database->queryFirstOrDefault(
			null,
			<<<SQL

			select
				users.user_id,
				users.level,
				user_authentications.current_password as password
			from
				users
				inner join
					user_authentications
					on
					(
						user_authentications.user_id = users.user_id
					)
			where
				users.login_id = :account_login_login_id
SQL
			/* AUTO FORMAT */,
			$parameters
		);
	}
}
