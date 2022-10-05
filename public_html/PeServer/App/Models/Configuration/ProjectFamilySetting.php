<?php

declare(strict_types=1);

namespace PeServer\App\Models\Configuration;

use PeServer\Core\Serialization\Mapping;

/**
 * Peプロジェクト設定。
 *
 * @immutable
 */
class ProjectFamilySetting
{
	#region variable

	#[Mapping(name: 'server_url')]
	public string $serverUrl;
	#[Mapping(name: 'contact_url')]
	public string $contactUrl;
	#[Mapping(name: 'app_project_url')]
	public string $appProjectUrl;
	#[Mapping(name: 'server_project_url')]
	public string $serverProjectUrl;
	#[Mapping(name: 'forum_url')]
	public string $forumUrl;
	#[Mapping(name: 'website_url')]
	public string $websiteUrl;
	#[Mapping(name: 'api_doc_url')]
	public string $apiDocUrl;

	#[Mapping(name: 'update_info_url_base')]
	public string $updateInfoUrlBase;

	#endregion
}
