<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\Mvc\Template\TemplateOptions;
use PeServer\Core\ProgramContext;
use PeServer\Core\Web\IUrlHelper;
use PeServer\Core\Web\WebSecurity;

class AppTemplateOptions extends TemplateOptions
{
	public function __construct(
		public string $controllerName,
		public ProgramContext $programContext,
		public IUrlHelper $urlHelper,
		public WebSecurity $webSecurity,
	) {
	}
}
