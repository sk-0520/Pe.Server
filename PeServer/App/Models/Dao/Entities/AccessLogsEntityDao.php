<?php

declare(strict_types=1);

namespace PeServer\App\Models\Dao\Entities;

use PeServer\App\Models\Data\Dto\AccessLogDto;
use PeServer\Core\Database\DaoBase;
use PeServer\Core\Database\DaoTrait;
use PeServer\Core\Database\IDatabaseContext;

class AccessLogsEntityDao extends DaoBase
{
	use DaoTrait;

	#region function

	public function insertAccessLog(AccessLogDto $accessLog): void
	{
		$this->context->insertSingle(
			<<<SQL

			insert into
				access_logs
				(
					timestamp,
					client_ip,
					client_host,
					request_id,
					session,
					ua,
					method,
					path,
					query,
					fragment,
					referer,
					running_time
				)
				values
				(
					:timestamp,
					:clientIp,
					:clientHost,
					:requestId,
					:session,
					:ua,
					:method,
					:path,
					:query,
					:fragment,
					:referer,
					:runningTime
				)

			SQL,
			[
				'timestamp' => $accessLog->timestamp,
				'clientIp' => $accessLog->clientIp,
				'clientHost' => $accessLog->clientHost,
				'requestId' => $accessLog->requestId,
				'session' => $accessLog->session,
				'ua' => $accessLog->ua,
				'method' => $accessLog->method,
				'path' => $accessLog->path,
				'query' => $accessLog->query,
				'fragment' => $accessLog->fragment,
				'referer' => $accessLog->referer,
				'runningTime' => $accessLog->runningTime,
			]
		);
	}

	#endregion
}
