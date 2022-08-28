<?php

declare(strict_types=1);

namespace PeServer\App\Models\Configuration;

use PeServer\App\Models\Configuration\PersistenceSetting;
use PeServer\Core\Serialization\Mapping;

/**
 * アドレス設定。
 *
 * @immutable
 */
class AddressSetting
{
	#region variable

	public string $domain;

	#[Mapping(name: 'public_url')]
	public string $publicUrl;

	#[Mapping(name: 'from_email')]
	public EmailAddressSetting $fromEmail;

	#[Mapping(name: 'return_email')]
	public string $returnEmail;

	public ProjectFamilySetting $families;

	#endregion
}
