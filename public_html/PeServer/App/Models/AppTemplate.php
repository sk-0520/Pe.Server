<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AppTemplateOptions;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\Template\ITemplateFactory;
use PeServer\Core\Mvc\Template\TemplateParameter;
use PeServer\Core\Web\UrlHelper;

/**
 * アプリ用汎用テンプレート処理。
 */
final class AppTemplate
{
	public function __construct(
		private AppConfiguration $config,
		private ITemplateFactory $templateFactory
	) {
	}

	/**
	 * テンプレートを適用。
	 *
	 * @param string $baseName
	 * @param string $templateName
	 * @param array<string,mixed> $params
	 * @return string
	 */
	private function buildTemplate(string $baseName, string $templateName, array $params, HttpStatus $status): string
	{
		$template = $this->templateFactory->createTemplate(new AppTemplateOptions('template' . DIRECTORY_SEPARATOR . $baseName, new UrlHelper('')));
		$result = $template->build($templateName . '.tpl', new TemplateParameter($status, $params, []));

		return $result;
	}

	/**
	 * メール用テンプレートの作成。
	 *
	 * @param string $templateName
	 * @param string $subject
	 * @param array<string,mixed> $params
	 * @return string
	 */
	public function createMailTemplate(string $templateName, string $subject, array $params): string
	{
		$families = $this->config->setting['config']['address']['families'];

		$params['app'] = [
			'subject' => $subject,
			'server_url' => $families['server_url'],
			'contact_url' => $families['contact_url'],
			'app_project_url' => $families['app_project_url'],
			'server_project_url' => $families['server_project_url'],
			'forum_url' => $families['forum_url'],
			'website_url' => $families['website_url'],
		];

		return $this->buildTemplate('email', $templateName, $params, HttpStatus::none());
	}
}
