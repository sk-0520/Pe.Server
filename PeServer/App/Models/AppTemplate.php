<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AppTemplateOptions;
use PeServer\App\Views\Emails\AppEmailTemplateBuilder;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\Template\ITemplateFactory;
use PeServer\Core\Mvc\Template\TemplateParameter;
use PeServer\Core\ProgramContext;
use PeServer\Core\Web\UrlHelper;
use PeServer\Core\Web\WebSecurity;

/**
 * アプリ用汎用テンプレート処理。
 */
final class AppTemplate
{
	public function __construct(
		private ProgramContext $programContext,
		private AppConfiguration $config,
		private ITemplateFactory $templateFactory,
		private WebSecurity $webSecurity,
	) {
	}

	#region function

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
		$template = $this->templateFactory->createTemplate(new AppTemplateOptions('template' . DIRECTORY_SEPARATOR . $baseName, $this->programContext, new UrlHelper(''), $this->webSecurity));
		$result = $template->build($templateName, new TemplateParameter($status, $params, []));

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
		$params = $this->addMailAppValues($subject, $params);

		return $this->buildTemplate('email', $templateName, $params, HttpStatus::None);
	}

	/**
	 * NodeTemplate によるメール用テンプレートの作成。
	 *
	 * 現在の送信経路と比較するための並行生成用。
	 *
	 * @param string $templateName
	 * @param string $subject
	 * @param array<string,mixed> $params
	 * @return string
	 */
	public function createNodeMailTemplate(string $templateName, string $subject, array $params): string
	{
		$params = $this->addMailAppValues($subject, $params);

		return (new AppEmailTemplateBuilder())->build($templateName, $subject, $params);
	}

	/**
	 * メールテンプレート共通のアプリ情報を設定。
	 *
	 * @param string $subject
	 * @param array<string,mixed> $params
	 * @return array<string,mixed>
	 */
	private function addMailAppValues(string $subject, array $params): array
	{
		$families = $this->config->setting->config->address->families;

		$params['app'] = [
			'subject' => $subject,
			'server_url' => $families->serverUrl,
			'contact_url' => $families->contactUrl,
			'app_project_url' => $families->appProjectUrl,
			'server_project_url' => $families->serverProjectUrl,
			'forum_url' => $families->forumUrl,
			'website_url' => $families->websiteUrl,
		];

		return $params;
	}

	#endregion
}
