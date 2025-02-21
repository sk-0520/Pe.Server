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
		IUrlHelper|null $urlHelper,
		SpecialStore|null $specialStore
	) {
		parent::__construct($environment, $revision, $urlHelper, $specialStore);
	}
}
