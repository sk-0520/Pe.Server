<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\Code;
use PeServer\Core\Text;
use PeServer\Core\Web\Url;

class AppUrl
{
	#region variable

	private Url|null $publicUrl = null;

	#endregion

	public function __construct(
		private AppConfiguration $appConfiguration
	) {
	}

	#region function

	public function getDomain(): string
	{
		return $this->appConfiguration->setting->config->address->domain;
	}

	public function getPublicUrl(): Url
	{
		if ($this->publicUrl === null) {
			$url = Text::replaceMap(
				Code::toLiteralString($this->appConfiguration->setting->config->address->publicUrl),
				[
					'DOMAIN' => $this->getDomain()
				]
			);

			$this->publicUrl = Url::parse($url);
		}

		return $this->publicUrl;
	}


	#endregion
}
