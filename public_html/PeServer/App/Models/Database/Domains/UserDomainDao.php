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
	 * @param string $loginId
	 * @return array{user_id:string,login_id:string,name:string,level:string,password:string}|null
	 */
	public function selectLoginUser(string $loginId)
	{
		return $this->database->queryFirstOrDefault(
			null,
			<<<SQL

			select
				users.user_id,
				users.login_id,
				users.name,
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
				and
				users.state = 'enabled'
SQL
			/* AUTO FORMAT */,
			[
				'account_login_login_id' => $loginId,
			]
		);
	}
}
