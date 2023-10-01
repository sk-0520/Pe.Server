<?php

declare(strict_types=1);

namespace PeServer\App\Models\Data\Dto;

use PeServer\Core\Database\DtoBase;
use PeServer\Core\Serialization\Mapping;
use PeServer\Core\Text;

class AccessLogDto extends DtoBase
{
	public string $timestamp = Text::EMPTY;

	#[Mapping(name: 'client_ip')]
	public string $clientIp = Text::EMPTY;

	#[Mapping(name: 'client_host')]
	public string $clientHost = Text::EMPTY;

	#[Mapping(name: 'request_id')]
	public string $requestId = Text::EMPTY;

	public string $session = Text::EMPTY;

	public string $ua = Text::EMPTY;

	public string $method = Text::EMPTY;

	#[Mapping(name: 'path')]
	public string $path = Text::EMPTY;
	#[Mapping(name: 'query')]
	public string $query = Text::EMPTY;
	#[Mapping(name: 'fragment')]
	public string $fragment = Text::EMPTY;

	public string $referer = Text::EMPTY;

	/** ミリ秒 */
	#[Mapping(name: 'running_time')]
	public float $runningTime = 0.0;
}
