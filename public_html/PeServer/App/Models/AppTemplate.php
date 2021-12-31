<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\HttpStatus;
use PeServer\Core\Mvc\Template;
use PeServer\Core\Mvc\TemplateParameter;

abstract class AppTemplate
{
	/**
	 * Undocumented function
	 *
	 * @param string $baseName
	 * @param string $templateName
	 * @param array<string,string|string[]|bool|int> $params
	 * @return string
	 */
	private static function createTemplate(string $baseName, string $templateName, array $params): string
	{
		$template = Template::create('template/' . $baseName);
		$result = $template->build($templateName . '.tpl', new TemplateParameter(HttpStatus::ok(), $params, []));

		return $result;
	}

	/**
	 * Undocumented function
	 *
	 * @param string $templateName
	 * @param string $subject
	 * @param array<string,string|string[]|bool|int> $params
	 * @return string
	 */
	public static function createMailTemplate(string $templateName, string $subject, array $params): string
	{
		$families = AppConfiguration::$json['config']['address']['families'];

		$params['app'] = [
			'subject' => $subject,
			'server_url' => $families['server_url'],
			'contact_url' => $families['contact_url'],
			'app_project_url' => $families['app_project_url'],
			'server_project_url' => $families['server_project_url'],
			'forum_url' => $families['forum_url'],
			'website_url' => $families['website_url'],
		];

		return self::createTemplate('email', $templateName, $params);
	}
}
