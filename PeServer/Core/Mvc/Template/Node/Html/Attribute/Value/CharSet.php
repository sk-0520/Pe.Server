<?php

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

enum CharSet: string
{
	case Utf8 = 'UTF-8';
	case Utf16 = 'UTF-16';
	case Iso88591 = 'ISO-8859-1';
	case UsAscii = 'US-ASCII';
	case Windows1252 = 'windows-1252';
}
