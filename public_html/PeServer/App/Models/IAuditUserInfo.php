<?php

declare(strict_types=1);

namespace PeServer\App\Models;

interface IAuditUserInfo
{
	public function getUserId(): string;
}
