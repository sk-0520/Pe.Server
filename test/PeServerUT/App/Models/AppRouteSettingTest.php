<?php

declare(strict_types=1);

namespace PeServerUT\App\Models;

use PeServer\App\Controllers\Page\AccountController;
use PeServer\App\Models\AppRouteSetting;
use PeServer\App\Models\Middleware\UserAccountFilterMiddleware;
use PeServer\App\Models\Middleware\NotLoginMiddleware;
use PeServer\App\Models\Middleware\SignupStep2FilterMiddleware;
use PeServer\Core\Environment;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Http\RequestPath;
use PeServer\Core\Mvc\Middleware\CsrfMiddleware;
use PeServer\Core\Web\IUrlHelper;
use PeServerTest\TestClass;

class AppRouteSettingTest extends TestClass
{
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
