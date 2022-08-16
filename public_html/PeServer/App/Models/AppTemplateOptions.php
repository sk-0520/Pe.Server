<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\Mvc\Template\TemplateOptions;
use PeServer\Core\Web\IUrlHelper;

class AppTemplateOptions extends TemplateOptions
{
	public function __construct(
		public string $controllerName,
		public IUrlHelper $urlHelper
	) {
	}
}
