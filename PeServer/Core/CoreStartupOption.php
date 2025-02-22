<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Web\IUrlHelper;
use PeServer\Core\Web\UrlHelper;

readonly class CoreStartupOption
{
	#region variable

	public IUrlHelper $urlHelper;

	#endregion

	public function __construct(
		public string $environment,
		public string $revision,
		public SpecialStore|null $specialStore,
		IUrlHelper|null $urlHelper
	) {
		$this->urlHelper = $urlHelper ?? new UrlHelper('');
	}
}
