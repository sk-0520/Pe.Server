<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\Template;
use PeServer\Core\Mvc\TemplateParameter;

abstract class AppTemplate
{
	/**
	 * テンプレートを適用。
	 *
	 * @param string $baseName
	 * @param string $templateName
	 * @param array<string,mixed> $params
	 * @return string
	 */
	private static function buildTemplate(string $baseName, string $templateName, array $params, HttpStatus $status): string
	{
		$template = Template::create('template/' . $baseName);
		$result = $template->build($templateName . '.tpl', new TemplateParameter($status, $params, []));

		return $result;
	}

	/**
	 * 汎用ページ用テンプレートの作成。
	 *
	 * @param string $templateName
	 * @param array<string,mixed> $params
	 * @param HttpStatus $status
	 * @return string
	 */
	public static function createPageTemplate(string $templateName, array $params, HttpStatus $status): string
	{
		return self::buildTemplate('page', $templateName, $params, $status);
	}

	/**
	 * メール用テンプレートの作成。
	 *
	 * @param string $templateName
	 * @param string $subject
	 * @param array<string,mixed> $params
	 * @return string
	 */
	public static function createMailTemplate(string $templateName, string $subject, array $params): string
	{
		$families = AppConfiguration::$config['config']['address']['families'];

		$params['app'] = [
			'subject' => $subject,
			'server_url' => $families['server_url'],
			'contact_url' => $families['contact_url'],
			'app_project_url' => $families['app_project_url'],
			'server_project_url' => $families['server_project_url'],
			'forum_url' => $families['forum_url'],
			'website_url' => $families['website_url'],
		];

		return self::buildTemplate('email', $templateName, $params, HttpStatus::none());
	}
}
