<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

enum TrackKind: string
{
	case Subtitles = 'subtitles';
	case Captions = 'captions';
	case Descriptions = 'descriptions';
	case Chapters = 'chapters';
	case Metadata = 'metadata';
}
