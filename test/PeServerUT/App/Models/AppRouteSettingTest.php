<?php

declare(strict_types=1);

namespace PeServerUT\App\Models;

use PeServer\App\Controllers\Page\AccountController;
use PeServer\App\Controllers\Page\HomeController;
use PeServer\App\Controllers\Page\PasswordController;
use PeServer\App\Controllers\Page\PluginController;
use PeServer\App\Models\AppRouteSetting;
use PeServer\App\Models\Middleware\UserAccountFilterMiddleware;
use PeServer\App\Models\Middleware\NotLoginMiddleware;
use PeServer\App\Models\Middleware\SignupStep2FilterMiddleware;
use PeServer\App\Models\Middleware\UserPluginEditFilterMiddleware;
use PeServer\App\Models\Middleware\PluginIdMiddleware;
use PeServer\Core\Environment;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Http\RequestPath;
use PeServer\Core\Mvc\Middleware\CsrfMiddleware;
use PeServer\Core\Web\IUrlHelper;
use PeServerTest\TestClass;

class AppRouteSettingTest extends TestClass
{
	public function test_node_root_routes(): void
	{
		$setting = new AppRouteSetting($this->createStub(Environment::class));
		$urlHelper = $this->createStub(IUrlHelper::class);
		foreach ([
			['/', HomeController::class, 'index', []],
			['/account', AccountController::class, 'index', []],
			['/plugin', PluginController::class, 'index', []],
			['/password', PasswordController::class, 'reminder_get', [NotLoginMiddleware::class]],
		] as [$path, $class, $methodName, $middleware]) {
			foreach ([HttpMethod::Get, HttpMethod::Head, HttpMethod::Post] as $method) {
				$matches = [];
				foreach ($setting->routes as $route) {
					$action = $route->getAction($method, new RequestPath($path, $urlHelper));
					if ($action !== null && $action->status === HttpStatus::None) {
						$matches[] = $action;
					}
				}
				if ($method === HttpMethod::Post) {
					$this->assertCount(0, $matches);
					continue;
				}
				$this->assertCount(1, $matches);
				$this->assertSame($class, $matches[0]->className);
				$this->assertSame($methodName, $matches[0]->actionSetting->controllerMethod);
				$this->assertTrue(method_exists($class, $methodName));
				$this->assertSame([], $matches[0]->params);
				$this->assertSame($middleware, $matches[0]->actionSetting->actionMiddleware);
			}
		}
	}

	public function test_plugin_routes(): void
	{
		$setting = new AppRouteSetting($this->createStub(Environment::class));
		$urlHelper = $this->createStub(IUrlHelper::class);
		$id = '12345678-1234-4234-8234-123456789abc';
		foreach ([
			'/plugin' => 'index',
			'/plugin/' . $id => 'detail',
		] as $path => $methodName) {
			foreach ([HttpMethod::Get, HttpMethod::Head, HttpMethod::Post] as $method) {
				$matches = [];
				foreach ($setting->routes as $route) {
					$action = $route->getAction($method, new RequestPath($path, $urlHelper));
					if ($action !== null && $action->status === HttpStatus::None) {
						$matches[] = $action;
					}
				}
				if ($method === HttpMethod::Post) {
					$this->assertCount(0, $matches);
					continue;
				}
				$this->assertCount(1, $matches);
				$this->assertSame(PluginController::class, $matches[0]->className);
				$this->assertSame($methodName, $matches[0]->actionSetting->controllerMethod);
				$isDetail = $methodName === 'detail';
				$this->assertSame($isDetail ? ['plugin_id' => $id] : [], $matches[0]->params);
				$this->assertSame($isDetail ? [PluginIdMiddleware::class] : [], $matches[0]->actionSetting->actionMiddleware);
			}
		}
	}

	public function test_password_routes(): void
	{
		$setting = new AppRouteSetting($this->createStub(Environment::class));
		$urlHelper = $this->createStub(IUrlHelper::class);
		$token = str_repeat('a', 80);
		foreach (['reminder', 'reminding', 'reset'] as $stage) {
			foreach ([HttpMethod::Get, HttpMethod::Head, HttpMethod::Post] as $method) {
				$matches = [];
				$path = "/password/$stage" . ($stage === 'reminder' ? '' : '/' . $token);
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
				$this->assertSame($stage . ($stage === 'reminding' ? '' : ($method === HttpMethod::Post ? '_post' : '_get')), $action->actionSetting->controllerMethod);
				$this->assertSame($stage === 'reminder' ? [] : ['token' => $token], $action->params);
				$this->assertSame($method === HttpMethod::Post
					? [NotLoginMiddleware::class, CsrfMiddleware::class]
					: [NotLoginMiddleware::class], $action->actionSetting->actionMiddleware);
			}
		}
	}

	public function test_account_family_routes(): void
	{
		$setting = new AppRouteSetting($this->createStub(Environment::class));
		$urlHelper = $this->createStub(IUrlHelper::class);
		$id = '12345678-1234-4234-8234-123456789abc';
		$routes = [
			['', 'index', [], [], false, false],
			['login', 'login', [], [], true, false],
			['logout', 'logout', [], [], false, false],
			['user', 'user', [UserAccountFilterMiddleware::class], [], false, false],
			['user/api', 'user_api', [UserAccountFilterMiddleware::class], [], true, false],
			['user/password', 'user_password', [UserAccountFilterMiddleware::class], [], true, false],
			['user/email', 'user_email', [UserAccountFilterMiddleware::class], [], true, false],
			['user/plugin', 'user_plugin_register', [UserAccountFilterMiddleware::class], [], true, false],
			['user/plugin/' . $id, 'user_plugin_update', [UserAccountFilterMiddleware::class, UserPluginEditFilterMiddleware::class], ['plugin_id' => $id], true, false],
			['user/plugin/reserve', 'user_plugin_reserve', [UserAccountFilterMiddleware::class], [], true, false],
			['user/audit-logs', 'user_audit_logs_top', [UserAccountFilterMiddleware::class], [], false, true],
			['user/audit-logs/page/3', 'user_audit_logs_page', [UserAccountFilterMiddleware::class], ['page_number' => '3'], false, true],
			['user/audit-logs/download', 'user_audit_logs_download', [UserAccountFilterMiddleware::class], [], false, true],
		];
		foreach ($routes as [$tail, $name, $middleware, $params, $hasPost, $getOnly]) {
			$path = '/account/' . $tail;
			foreach ([HttpMethod::Get, HttpMethod::Head, HttpMethod::Post] as $method) {
				$matches = [];
				foreach ($setting->routes as $route) {
					$action = $route->getAction($method, new RequestPath($path, $urlHelper));
					if ($action !== null && $action->status === HttpStatus::None) {
						$matches[] = $action;
					}
				}
				if (($getOnly && $method === HttpMethod::Head) || (!$hasPost && $method === HttpMethod::Post)) {
					$this->assertCount(0, $matches);
					continue;
				}
				$this->assertCount(1, $matches, $method->value . ' ' . $path);
				$action = $matches[0];
				$this->assertSame(AccountController::class, $action->className);
				$this->assertSame($name . ($hasPost ? ($method === HttpMethod::Post ? '_post' : '_get') : ''), $action->actionSetting->controllerMethod);
				$this->assertTrue(method_exists($action->className, $action->actionSetting->controllerMethod));
				$this->assertSame($params, $action->params);
				$this->assertSame(
					$method === HttpMethod::Post ? [...$middleware, CsrfMiddleware::class] : $middleware,
					$action->actionSetting->actionMiddleware
				);
			}
		}
	}

	public function test_signup_routes(): void
	{
		$setting = new AppRouteSetting($this->createStub(Environment::class));
		$urlHelper = $this->createStub(IUrlHelper::class);
		$token = str_repeat('a', 80);
		foreach (['' => 'step1', '/notify' => 'notify', '/' . $token => 'step2'] as $tail => $stage) {
			foreach ([HttpMethod::Get, HttpMethod::Head, HttpMethod::Post] as $method) {
				if ($stage === 'notify' && $method === HttpMethod::Post) {
					continue;
				}
				$matches = [];
				foreach ($setting->routes as $route) {
					$action = $route->getAction($method, new RequestPath('/account/signup' . $tail, $urlHelper));
					if ($action !== null && $action->status === HttpStatus::None) {
						$matches[] = $action;
					}
				}
				$this->assertCount(1, $matches);
				$action = $matches[0];
				$this->assertSame(AccountController::class, $action->className);
				$this->assertSame(
					'signup_' . $stage . ($stage === 'notify' ? '' : ($method === HttpMethod::Post ? '_post' : '_get')),
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

	public function test_user_edit_routes(): void
	{
		$setting = new AppRouteSetting($this->createStub(Environment::class));
		$urlHelper = $this->createStub(IUrlHelper::class);
		foreach ([HttpMethod::Get, HttpMethod::Head, HttpMethod::Post] as $method) {
			$path = new RequestPath('/account/user/edit', $urlHelper);
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
			$this->assertSame('user_edit_' . ($method === HttpMethod::Post ? 'post' : 'get'), $action->actionSetting->controllerMethod);
			$this->assertSame(
				$method === HttpMethod::Post
					? [UserAccountFilterMiddleware::class, CsrfMiddleware::class]
					: [UserAccountFilterMiddleware::class],
				$action->actionSetting->actionMiddleware
			);
		}
	}

	public function test_migration_routes_are_removed(): void
	{
		$setting = new AppRouteSetting($this->createStub(Environment::class));
		$urlHelper = $this->createStub(IUrlHelper::class);
		$token = str_repeat('a', 80);
		$id = '12345678-1234-4234-8234-123456789abc';
		$paths = [
			'/index2', '/account2', '/account2/login', '/account2/logout',
			'/account2/signup', '/account2/signup/notify', '/account2/signup/' . $token,
			'/account/index2', '/account/login2', '/account/logout2', '/account/signup2',
			'/account/signup2/notify', '/account/signup2/' . $token,
			'/password2', '/password2/reminder', '/password2/reminding/' . $token, '/password2/reset/' . $token,
			'/password/reminder2', '/password/reminding2/' . $token, '/password/reset2/' . $token,
			'/plugin2', '/plugin2/' . $id, '/plugin/index2', '/plugin/detail2/' . $id,
		];
		foreach ([
			'user', 'user/edit', 'user/api', 'user/password', 'user/email', 'user/plugin',
			'user/plugin/reserve', 'user/audit-logs', 'user/audit-logs/download',
		] as $tail) {
			$paths[] = '/account2/' . $tail;
			$paths[] = '/account/' . $tail . '2';
		}
		$paths[] = '/account2/user/plugin/' . $id;
		$paths[] = '/account/user/plugin2/' . $id;
		$paths[] = '/account2/user/audit-logs/page/2';
		$paths[] = '/account/user/audit-logs2/page/2';
		foreach ($paths as $path) {
			foreach ([HttpMethod::Get, HttpMethod::Head, HttpMethod::Post] as $method) {
				foreach ($setting->routes as $route) {
					$action = $route->getAction($method, new RequestPath($path, $urlHelper));
					$this->assertTrue($action === null || $action->status !== HttpStatus::None, $method->value . ' ' . $path);
				}
			}
		}
	}
}
