<?php

declare(strict_types=1);

namespace PeServerUT\App\Models;

use PeServer\App\Controllers\Page\AccountController;
use PeServer\App\Models\AppRouteSetting;
use PeServer\App\Models\Middleware\UserAccountFilterMiddleware;
use PeServer\Core\Environment;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Http\RequestPath;
use PeServer\Core\Mvc\Middleware\CsrfMiddleware;
use PeServer\Core\Web\IUrlHelper;
use PeServerTest\TestClass;

class AppRouteSettingTest extends TestClass
{
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
