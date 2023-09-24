<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\App\Controllers\Api\AccountApiController;
use PeServer\App\Controllers\Api\AdministratorApiController;
use PeServer\App\Controllers\Api\ApplicationApiController;
use PeServer\App\Controllers\Api\DevelopmentApiController;
use PeServer\App\Controllers\Api\PluginApiController;
use PeServer\App\Controllers\Page\AccountController;
use PeServer\App\Controllers\Page\AjaxController;
use PeServer\App\Controllers\Page\HomeController;
use PeServer\App\Controllers\Page\ManagementControlController;
use PeServer\App\Controllers\Page\ManagementController;
use PeServer\App\Controllers\Page\PasswordController;
use PeServer\App\Controllers\Page\PluginController;
use PeServer\App\Controllers\Page\ToolController;
use PeServer\App\Models\Middleware\AdministratorAccountFilterMiddleware;
use PeServer\App\Models\Middleware\Api\ApiAcaoMiddleware;
use PeServer\App\Models\Middleware\Api\ApiAdministratorAccountFilterMiddleware;
use PeServer\App\Models\Middleware\Api\ApiUserAccountFilterMiddleware;
use PeServer\App\Models\Middleware\DevelopmentMiddleware;
use PeServer\App\Models\Middleware\NotLoginMiddleware;
use PeServer\App\Models\Middleware\PluginIdMiddleware;
use PeServer\App\Models\Middleware\SetupAccountFilterMiddleware;
use PeServer\App\Models\Middleware\SignupStep2FilterMiddleware;
use PeServer\App\Models\Middleware\UserAccountFilterMiddleware;
use PeServer\App\Models\Middleware\UserPluginEditFilterMiddleware;
use PeServer\Core\Environment;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Mvc\Middleware\CsrfMiddleware;
use PeServer\Core\Mvc\Middleware\PerformanceMiddleware;
use PeServer\Core\Mvc\Middleware\PerformanceShutdownMiddleware;
use PeServer\Core\Mvc\Route;
use PeServer\Core\Mvc\RouteSetting;
use PeServer\Core\Text;

/**
 * アプリルーティング設定。
 */
final class AppRouteSetting extends RouteSetting
{
	private const SIGNUP_TOKEN = '[a-zA-Z0-9]{80}';
	private const PASSWORD_REMINDER_TOKEN = '[a-zA-Z0-9]{80}';
	private const PLUGIN_ID = '\{?[a-fA-F0-9\-]{32,}\}?';

	public function __construct()
	{
		$isProduction = Environment::isProduction();
		parent::__construct(
			[],
			[
				...($isProduction ? [PerformanceMiddleware::class] : [])
			],
			[
				...($isProduction ? [PerformanceShutdownMiddleware::class] : [])
			],
			[],
			[
				(new Route(Text::EMPTY, HomeController::class))
					->addAction('about', HttpMethod::gets(), 'about')
					->addAction('about/privacy', HttpMethod::gets(), 'privacy')
					->addAction('about/contact', HttpMethod::gets(), 'contact_get')
					->addAction('about/contact', HttpMethod::Post, 'contact_post')
					->addAction('api-doc', HttpMethod::gets(), 'api')
					->addAction('dev/exception', HttpMethod::gets(), 'exception', [DevelopmentMiddleware::class])
					->addAction(':path@[a-zA-z0-9_\(\)\-]+\.[a-zA-z0-9_\(\)\-]+', HttpMethod::gets(), 'wildcard')
				/* AUTO-FORMAT */,
				(new Route('account', AccountController::class))
					->addAction('login', HttpMethod::gets(), 'login_get')
					->addAction('login', HttpMethod::Post, 'login_post')
					->addAction('logout', HttpMethod::gets())
					->addAction('signup', HttpMethod::gets(), 'signup_step1_get', [NotLoginMiddleware::class])
					->addAction('signup', HttpMethod::Post, 'signup_step1_post', [NotLoginMiddleware::class, CsrfMiddleware::class])
					->addAction('signup/notify', HttpMethod::gets(), 'signup_notify', [NotLoginMiddleware::class])
					->addAction('signup/:token@' . self::SIGNUP_TOKEN, HttpMethod::gets(), 'signup_step2_get', [NotLoginMiddleware::class, SignupStep2FilterMiddleware::class])
					->addAction('signup/:token@' . self::SIGNUP_TOKEN, HttpMethod::Post, 'signup_step2_post', [NotLoginMiddleware::class, SignupStep2FilterMiddleware::class, CsrfMiddleware::class])
					->addAction('user', HttpMethod::gets(), Route::DEFAULT_METHOD, [UserAccountFilterMiddleware::class])
					->addAction('user/edit', HttpMethod::gets(), 'user_edit_get', [UserAccountFilterMiddleware::class])
					->addAction('user/edit', HttpMethod::Post, 'user_edit_post', [UserAccountFilterMiddleware::class, CsrfMiddleware::class])
					->addAction('user/api', HttpMethod::gets(), 'user_api_get', [UserAccountFilterMiddleware::class])
					->addAction('user/api', HttpMethod::Post, 'user_api_post', [UserAccountFilterMiddleware::class])
					->addAction('user/password', HttpMethod::gets(), 'user_password_get', [UserAccountFilterMiddleware::class])
					->addAction('user/password', HttpMethod::Post, 'user_password_post', [UserAccountFilterMiddleware::class, CsrfMiddleware::class])
					->addAction('user/email', HttpMethod::gets(), 'user_email_get', [UserAccountFilterMiddleware::class])
					->addAction('user/email', HttpMethod::Post, 'user_email_post', [UserAccountFilterMiddleware::class, CsrfMiddleware::class])
					->addAction('user/plugin', HttpMethod::gets(), 'user_plugin_register_get', [UserAccountFilterMiddleware::class])
					->addAction('user/plugin', HttpMethod::Post, 'user_plugin_register_post', [UserAccountFilterMiddleware::class, CsrfMiddleware::class])
					->addAction('user/plugin/:plugin_id@' . self::PLUGIN_ID, HttpMethod::gets(), 'user_plugin_update_get', [UserAccountFilterMiddleware::class, UserPluginEditFilterMiddleware::class])
					->addAction('user/plugin/:plugin_id@' . self::PLUGIN_ID, HttpMethod::Post, 'user_plugin_update_post', [UserAccountFilterMiddleware::class, CsrfMiddleware::class, UserPluginEditFilterMiddleware::class])
					->addAction('user/audit-logs', HttpMethod::Get, 'user_audit_logs', [UserAccountFilterMiddleware::class])
				/* AUTO-FORMAT */,
				(new Route('password', PasswordController::class, [NotLoginMiddleware::class]))
					->addAction('reminder', HttpMethod::gets(), 'reminder_get')
					->addAction('reminder', HttpMethod::Post, 'reminder_post', [CsrfMiddleware::class])
					->addAction('reminding/:token@' . self::PASSWORD_REMINDER_TOKEN, HttpMethod::Get, 'reminding')
					->addAction('reset/:token@' . self::PASSWORD_REMINDER_TOKEN, HttpMethod::Get, 'reset_get')
					->addAction('reset/:token@' . self::PASSWORD_REMINDER_TOKEN, HttpMethod::Post, 'reset_post', [CsrfMiddleware::class])
				/* AUTO-FORMAT */,
				(new Route('plugin', PluginController::class))
					->addAction(':plugin_id@' . self::PLUGIN_ID, HttpMethod::gets(), 'detail', [PluginIdMiddleware::class])
				/* AUTO-FORMAT */,
				(new Route('management', ManagementController::class, [AdministratorAccountFilterMiddleware::class]))
					->addAction('setup', HttpMethod::Get, 'setup_get', [Route::CLEAR_MIDDLEWARE, SetupAccountFilterMiddleware::class])
					->addAction('setup', HttpMethod::Post, 'setup_post', [Route::CLEAR_MIDDLEWARE, SetupAccountFilterMiddleware::class])
					->addAction('environment', HttpMethod::Get)
					->addAction('configuration', HttpMethod::Get)
					->addAction('configuration/edit', HttpMethod::Get, 'configuration_edit_get')
					->addAction('configuration/edit', HttpMethod::Post, 'configuration_edit_post', [CsrfMiddleware::class])
					->addAction('backup', HttpMethod::Post, 'backup', [CsrfMiddleware::class])
					->addAction('delete-old-data', HttpMethod::Post, 'delete_old_data', [CsrfMiddleware::class])
					->addAction('database-maintenance', HttpMethod::Get, 'database_maintenance_get')
					->addAction('database-maintenance', HttpMethod::Post, 'database_maintenance_post', [CsrfMiddleware::class])
					->addAction('database-download', HttpMethod::Get, 'database_download_get')
					->addAction('mail-send', HttpMethod::Get, 'mail_send_get')
					->addAction('mail-send', HttpMethod::Post, 'mail_send_post')
					->addAction('php-evaluate', HttpMethod::Get, 'php_evaluate_get')
					->addAction('php-evaluate', HttpMethod::Post, 'php_evaluate_post', [CsrfMiddleware::class])
					->addAction('default-plugin', HttpMethod::Get, 'default_plugin_get')
					->addAction('default-plugin', HttpMethod::Post, 'default_plugin_post', [CsrfMiddleware::class])
					->addAction('delete-old-data', HttpMethod::Post, 'delete_old_data', [CsrfMiddleware::class])
					->addAction('cache-rebuild', HttpMethod::Post, 'cache_rebuild', [CsrfMiddleware::class])
					->addAction('clear-deploy-progress', HttpMethod::Post, 'clear_deploy_progress', [CsrfMiddleware::class])
					->addAction('plugin-category', HttpMethod::Get, 'plugin_category_get')
					->addAction('feedback', HttpMethod::Get, 'feedback_list_top')
					->addAction('feedback/page/:page_number@\d++', HttpMethod::Get, 'feedback_list_page')
					->addAction('feedback/:sequence@\d++', HttpMethod::Get, 'feedback_detail_get')
					->addAction('feedback/:sequence@\d++', HttpMethod::Post, 'feedback_detail_post')
					->addAction('crash-report', HttpMethod::Get, 'crash_report_list_top')
					->addAction('crash-report/page/:page_number@\d++', HttpMethod::Get, 'crash_report_list_page')
					->addAction('crash-report/:sequence@\d++', HttpMethod::Get, 'crash_report_detail_get')
					->addAction('crash-report/:sequence@\d++', HttpMethod::Post, 'crash_report_detail_post')
					->addAction('version', HttpMethod::Get, 'version_get')
					->addAction('version', HttpMethod::Post, 'version_post')
					->addAction('log', HttpMethod::Get, 'log_list')
					->addAction('log/:log_name@\w+\.log', HttpMethod::Get, 'log_detail_get')
					->addAction('log/:log_name@\w+\.log', HttpMethod::Post, 'log_detail_post')
					->addAction('markdown', HttpMethod::Get, 'markdown')
				/* AUTO-FORMAT */,
				(new Route('management/control', ManagementControlController::class, [AdministratorAccountFilterMiddleware::class]))
					->addAction('user', HttpMethod::Get, 'user_list_get')
				/* AUTO-FORMAT */,
				(new Route('tool', ToolController::class))
					->addAction('base64', HttpMethod::Get, 'base64_get')
					->addAction('base64', HttpMethod::Post, 'base64_post')
					->addAction('json', HttpMethod::Get, 'json_get')
					->addAction('json', HttpMethod::Post, 'json_post')
				/* AUTO-FORMAT */,
				(new Route('ajax', AjaxController::class, [UserAccountFilterMiddleware::class]))
					->addAction('markdown', HttpMethod::Post, 'markdown')
					->addAction('plugin-category', HttpMethod::Post, 'plugin_category_post', [CsrfMiddleware::class, AdministratorAccountFilterMiddleware::class])
					->addAction('plugin-category/:plugin_category_id@.+', HttpMethod::Patch, 'plugin_category_patch', [CsrfMiddleware::class, AdministratorAccountFilterMiddleware::class])
					->addAction('plugin-category/:plugin_category_id@.+', HttpMethod::Delete, 'plugin_category_delete', [CsrfMiddleware::class, AdministratorAccountFilterMiddleware::class])
					->addAction('log/:log_name@\w+\.log', HttpMethod::Delete, 'log_delete', [CsrfMiddleware::class, AdministratorAccountFilterMiddleware::class])
					->addAction('feedback/:sequence@\d++', HttpMethod::Delete, 'feedback_delete', [CsrfMiddleware::class, AdministratorAccountFilterMiddleware::class])
					->addAction('crash-report/:sequence@\d++', HttpMethod::Delete, 'crash_report_delete', [CsrfMiddleware::class, AdministratorAccountFilterMiddleware::class])
				/* AUTO-FORMAT */,
				(new Route('api/development', DevelopmentApiController::class, [DevelopmentMiddleware::class]))
					->addAction('initialize', HttpMethod::Post)
					->addAction('administrator', HttpMethod::Post)
				/* AUTO-FORMAT */,
				(new Route('api/plugin', PluginApiController::class, [ApiAcaoMiddleware::class]))
					->addAction('exists', HttpMethod::Post, 'exists')
					->addAction('generate-plugin-id', HttpMethod::gets(), 'generate_plugin_id')
					->addAction('information', HttpMethod::Post, 'information')
				/* AUTO-FORMAT */,
				(new Route('api/application', ApplicationApiController::class, [ApiAcaoMiddleware::class]))
					->addAction('feedback', HttpMethod::Post, 'feedback')
					->addAction('crash-report', HttpMethod::Post, 'crash_report')
					->addAction('version/update', HttpMethod::Get, 'version_update')
				/* AUTO-FORMAT */,
				(new Route('api/account', AccountApiController::class, [ApiAcaoMiddleware::class, ApiUserAccountFilterMiddleware::class, ApiAdministratorAccountFilterMiddleware::class]))
				/* AUTO-FORMAT */,
				(new Route('api/administrator', AdministratorApiController::class, [ApiAcaoMiddleware::class, ApiAdministratorAccountFilterMiddleware::class]))
					->addAction('backup', HttpMethod::Post, 'backup')
					->addAction('delete-old-data', HttpMethod::Post, 'delete_old_data')
					->addAction('cache-rebuild', HttpMethod::Post, 'cache_rebuild')
					->addAction('deploy/:mode@.+', HttpMethod::Post, 'deploy')
					->addAction('pe/version', HttpMethod::Post, 'pe_version')
				/* AUTO-FORMAT */,
			]
		);
	}
}
