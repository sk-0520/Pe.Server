<?php

declare(strict_types=1);

namespace PeServerUT\App\Models;

use PeServer\App\Controllers\Page\AccountController;
use PeServer\App\Controllers\Page\PasswordController;
use PeServer\App\Models\AppRouteSetting;
use PeServer\App\Models\Middleware\UserAccountFilterMiddleware;
use PeServer\App\Models\Middleware\NotLoginMiddleware;
use PeServer\App\Models\Middleware\SignupStep2FilterMiddleware;
use PeServer\App\Models\Middleware\UserPluginEditFilterMiddleware;
use PeServer\Core\Environment;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Http\RequestPath;
use PeServer\Core\Mvc\Middleware\CsrfMiddleware;
use PeServer\Core\Web\IUrlHelper;
use PeServerTest\TestClass;

class AppRouteSettingTest extends TestClass
{
	public function test_password_routes(): void
	{
		$setting = new AppRouteSetting($this->createStub(Environment::class));
		$urlHelper = $this->createStub(IUrlHelper::class);
		$token = str_repeat('a', 80);
		foreach (['', '2'] as $suffix) {
			foreach (['reminder', 'reminding', 'reset'] as $stage) {
				foreach ([HttpMethod::Get, HttpMethod::Head, HttpMethod::Post] as $method) {
					$matches = [];
					$path = "/password/$stage$suffix" . ($stage === 'reminder' ? '' : '/' . $token);
					foreach ($setting->routes as $route) {
						$action = $route->getAction($method, new RequestPath($path, $urlHelper));
						if ($action !== null && $action->status === HttpStatus::None) {
							$matches[] = $action;
						}
					}
					if (($stage !== 'reminder' && $method === HttpMethod::Head) || ($stage === 'reminding' && $method === HttpMethod::Post)) {
						$this->assertCount(0, $matches);
						continue;
					}
					$this->assertCount(1, $matches);
					$action = $matches[0];
					$this->assertSame(PasswordController::class, $action->className);
					$this->assertSame($stage . ($stage === 'reminding' ? '' : ($method === HttpMethod::Post ? '_post' : '_get')) . $suffix, $action->actionSetting->controllerMethod);
					$this->assertSame($stage === 'reminder' ? [] : ['token' => $token], $action->params);
					$this->assertSame($method === HttpMethod::Post
						? [NotLoginMiddleware::class, CsrfMiddleware::class]
						: [NotLoginMiddleware::class], $action->actionSetting->actionMiddleware);
				}
			}
		}
	}

	public function test_account_family_routes(): void
	{
		$setting = new AppRouteSetting($this->createStub(Environment::class));
		$urlHelper = $this->createStub(IUrlHelper::class);
		$id = '12345678-1234-4234-8234-123456789abc';
		$routes = [
			['', 'index2', 'index', [], [], false, false],
			['logout', 'logout2', 'logout', [], [], false, false],
			['user', 'user2', 'user', [UserAccountFilterMiddleware::class], [], false, false],
			['user/api', 'user/api2', 'user_api', [UserAccountFilterMiddleware::class], [], true, false],
			['user/password', 'user/password2', 'user_password', [UserAccountFilterMiddleware::class], [], true, false],
			['user/email', 'user/email2', 'user_email', [UserAccountFilterMiddleware::class], [], true, false],
			['user/plugin', 'user/plugin2', 'user_plugin_register', [UserAccountFilterMiddleware::class], [], true, false],
			['user/plugin/' . $id, 'user/plugin2/' . $id, 'user_plugin_update', [UserAccountFilterMiddleware::class, UserPluginEditFilterMiddleware::class], ['plugin_id' => $id], true, false],
			['user/plugin/reserve', 'user/plugin/reserve2', 'user_plugin_reserve', [UserAccountFilterMiddleware::class], [], true, false],
			['user/audit-logs', 'user/audit-logs2', 'user_audit_logs_top', [UserAccountFilterMiddleware::class], [], false, true],
			['user/audit-logs/page/3', 'user/audit-logs2/page/3', 'user_audit_logs_page', [UserAccountFilterMiddleware::class], ['page_number' => '3'], false, true],
			['user/audit-logs/download', 'user/audit-logs/download2', 'user_audit_logs_download', [UserAccountFilterMiddleware::class], [], false, true],
		];
		foreach ($routes as [$oldPath, $newPath, $name, $middleware, $params, $hasPost, $getOnly]) {
			foreach ([$oldPath => '', $newPath => '2'] as $path => $suffix) {
				foreach ([HttpMethod::Get, HttpMethod::Head, HttpMethod::Post] as $method) {
					$matches = [];
					foreach ($setting->routes as $route) {
						$action = $route->getAction($method, new RequestPath('/account/' . $path, $urlHelper));
						if ($action !== null && $action->status === HttpStatus::None) {
							$matches[] = $action;
						}
					}
					if (($getOnly && $method === HttpMethod::Head) || (!$hasPost && $method === HttpMethod::Post)) {
						$this->assertCount(0, $matches);
						continue;
					}
					$this->assertCount(1, $matches, $method->value . ' /account/' . $path);
					$action = $matches[0];
					$this->assertSame(AccountController::class, $action->className);
					$this->assertSame($name . ($hasPost ? ($method === HttpMethod::Post ? '_post' : '_get') : '') . $suffix, $action->actionSetting->controllerMethod);
					$this->assertSame($params, $action->params);
					$this->assertSame(
						$method === HttpMethod::Post ? [...$middleware, CsrfMiddleware::class] : $middleware,
						$action->actionSetting->actionMiddleware
					);
				}
			}
		}
	}

	public function test_signup_routes(): void
	{
		$setting = new AppRouteSetting($this->createStub(Environment::class));
		$urlHelper = $this->createStub(IUrlHelper::class);
		$token = str_repeat('a', 80);
		foreach (['', '2'] as $suffix) {
			foreach (['' => 'step1', '/notify' => 'notify', '/' . $token => 'step2'] as $tail => $stage) {
				foreach ([HttpMethod::Get, HttpMethod::Head, HttpMethod::Post] as $method) {
					if ($stage === 'notify' && $method === HttpMethod::Post) {
						continue;
					}
					$matches = [];
					foreach ($setting->routes as $route) {
						$action = $route->getAction($method, new RequestPath('/account/signup' . $suffix . $tail, $urlHelper));
						if ($action !== null && $action->status === HttpStatus::None) {
							$matches[] = $action;
						}
					}
					$this->assertCount(1, $matches);
					$action = $matches[0];
					$this->assertSame(AccountController::class, $action->className);
					$this->assertSame(
						'signup_' . $stage . ($stage === 'notify' ? '' : ($method === HttpMethod::Post ? '_post' : '_get')) . $suffix,
						$action->actionSetting->controllerMethod
					);
					$middleware = [NotLoginMiddleware::class];
					if ($stage === 'step2') {
						$middleware[] = SignupStep2FilterMiddleware::class;
						$this->assertSame(['token' => $token], $action->params);
					}
					if ($method === HttpMethod::Post) {
						$middleware[] = CsrfMiddleware::class;
					}
					$this->assertSame($middleware, $action->actionSetting->actionMiddleware);
				}
			}
		}
	}

	public function test_user_edit_routes(): void
	{
		$setting = new AppRouteSetting($this->createStub(Environment::class));
		$urlHelper = $this->createStub(IUrlHelper::class);
		foreach (['', '2'] as $suffix) {
			foreach ([HttpMethod::Get, HttpMethod::Head, HttpMethod::Post] as $method) {
				$path = new RequestPath('/account/user/edit' . $suffix, $urlHelper);
				$matches = [];
				foreach ($setting->routes as $route) {
					$action = $route->getAction($method, $path);
					if ($action !== null && $action->status === HttpStatus::None) {
						$matches[] = $action;
					}
				}
				$this->assertCount(1, $matches);
				$action = $matches[0];
				$this->assertSame(HttpStatus::None, $action->status);
				$this->assertSame(AccountController::class, $action->className);
				$this->assertSame('user_edit_' . ($method === HttpMethod::Post ? 'post' : 'get') . $suffix, $action->actionSetting->controllerMethod);
				$this->assertSame(
					$method === HttpMethod::Post
						? [UserAccountFilterMiddleware::class, CsrfMiddleware::class]
						: [UserAccountFilterMiddleware::class],
					$action->actionSetting->actionMiddleware
				);
			}
		}
	}
}
