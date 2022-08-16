<?php

declare(strict_types=1);

namespace PeServer\Core\Web;

use PeServer\Core\Web\IUrlHelper;

class UrlHelper implements IUrlHelper
{
	public function __construct(
		private string $basePath
	) {
	}

	public static function none(): self
	{
		return new self('');
	}

	//[IUrlHelper]

	public function getBasePath(): string
	{
		return $this->basePath;
	}
}
