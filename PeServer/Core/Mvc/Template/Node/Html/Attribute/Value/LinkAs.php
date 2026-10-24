<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

enum LinkAs: string
{
	case Audio = "audio";
	case Document = "document";
	case Embed = "embed";
	case Fetch = "fetch";
	case Font = "font";
	case Image = "image";
	case Json = "json";
	case Object = "object";
	case Script = "script";
	case Style = "style";
	case Track = "track";
	case Video = "video";
	case Worker = "worker";
}
