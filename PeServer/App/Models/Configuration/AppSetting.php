<?php

declare(strict_types=1);

namespace PeServer\App\Models\Configuration;

use PeServer\App\Models\Configuration\PersistenceSetting;
use PeServer\Core\Serialization\Mapping;

/**
 * アプリ設定。
 *
 * @immutable
 */
class AppSetting
{
	#region variable

	public PersistenceSetting $persistence;

	public LoggingSetting $logging;

	public StoreSetting $store;

	public CacheSetting $cache;

	#[Mapping("access_log")]
	public AccessLogSetting $accessLog;

	public CryptoSetting $crypto;

	public ConfigurationSetting $config;

	public MailSetting $mail;

	public ApiSetting $api;

	public DebugSetting $debug;

	#endregion

	public function __construct()
	{
		$this->debug = new DebugSetting();
	}
}
