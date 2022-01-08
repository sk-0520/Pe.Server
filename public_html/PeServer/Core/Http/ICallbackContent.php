<?php

declare(strict_types=1);

namespace PeServer\Core\Http;

interface ICallbackContent
{
	public function output(): void;
}
