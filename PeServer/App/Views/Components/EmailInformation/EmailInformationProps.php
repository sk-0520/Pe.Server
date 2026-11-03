<?php

declare(strict_types=1);

namespace PeServer\App\Views\Components\EmailInformation;

use PeServer\App\Models\AppEmailInformation;
use PeServer\Core\Mvc\Template\Node\Props;

readonly class EmailInformationProps extends Props
{
	public function __construct(public AppEmailInformation $email)
	{
		//NOP
	}
}
