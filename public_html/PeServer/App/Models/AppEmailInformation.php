<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\App\Models\AppConfiguration;
use PeServer\Core\Text;

readonly class AppEmailInformation
{
	#region variable

	public string $address;
	public string $domain;

	#endregion

	public function __construct(AppConfiguration $config)
	{
		$this->address = $config->setting->config->address->fromEmail->address;
		$this->domain = Text::split($this->address, '@')[1];
	}
}
