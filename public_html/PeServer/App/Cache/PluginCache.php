<?php

declare(strict_types=1);

namespace PeServer\App\Cache;

class PluginCache
{
	public string $pluginId;
	public string $userId;
	public string $pluginName;
	public string $displayName;
	public string $pluginState;
	public string $description;
	/**
	 * Undocumented variable
	 *
	 * @var array<string,string>
	 */
	public array $urls;
}
