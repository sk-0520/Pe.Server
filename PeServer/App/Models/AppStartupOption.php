<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\CoreStartupOption;
use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Web\IUrlHelper;

readonly class AppStartupOption extends CoreStartupOption
{
	public function __construct(
		string $environment,
		string $revision,
		SpecialStore|null $specialStore,
		IUrlHelper|null $urlHelper = null
	) {
		parent::__construct($environment, $revision, $specialStore, $urlHelper);
	}
}
