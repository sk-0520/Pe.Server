<?php

declare(strict_types=1);

namespace PeServer\Core\Mail;

enum SendMode: int
{
	case Unknown = 0;
	case Smtp = 1;
}
