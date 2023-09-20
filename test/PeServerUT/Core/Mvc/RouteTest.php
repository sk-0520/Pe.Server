<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc;

use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\RequestPath;
use PeServer\Core\Mvc\Route;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Web\UrlHelper;
use PeServerTest\Data;
use PeServerTest\TestClass;

class RouteTest extends TestClass
{
	public function test_construct_exception_slash()
	{
		$this->expectException(ArgumentException::class);
		new Route('/', 'ClassName');
		$this->fail();
	}

	public function test_construct_exception_start()
	{
		$this->expectException(ArgumentException::class);
		new Route('/root', 'ClassName');
		$this->fail();
	}

	public function test_construct_exception_end()
	{
		$this->expectException(ArgumentException::class);
		new Route('root/', 'ClassName');
		$this->fail();
	}

	public function test_getAction()
	{
		$tests = [
			[
				'route' => ['path', 'Controller'],
				'actions' => [
					['action', HttpMethod::Get, "1"],
					['action/action', HttpMethod::Get, "2"],
				],
				'request' => [
					[
						'expected' => ['Controller', '1'],
						'input' => [HttpMethod::Get, new RequestPath('path/action', new UrlHelper(''))]
					],
					[
						'expected' => ['Controller', '2'],
						'input' => [HttpMethod::Get, new RequestPath('path/action/action', new UrlHelper(''))]
					],
				]
			],
			[
				'route' => ['api/test', 'TestController'],
				'actions' => [
					['list', HttpMethod::Post],
				],
				'request' => [
					[
						'expected' => ['TestController', 'list'],
						'input' => [HttpMethod::Post, new RequestPath('api/test/list', new UrlHelper(''))]
					],
				]
			],
			[
				'route' => ['controller', 'UrlParamController'],
				'actions' => [
					['input/:value', HttpMethod::Get, 'input1'],
					[':value/input', HttpMethod::Get, 'input2'],
					['input/reg/:value@\\d+', HttpMethod::Get, 'input3'],
					['multi/:value1@\\d+/:value2/:value3@[a-z]+/none', HttpMethod::Get, 'input4'],
				],
				'request' => [
					[
						'expected' => ['UrlParamController', 'input1', ['value' => '123']],
						'input' => [HttpMethod::Get, new RequestPath('controller/input/123', new UrlHelper(''))]
					],
					[
						'expected' => ['UrlParamController', 'input2', ['value' => '123']],
						'input' => [HttpMethod::Get, new RequestPath('controller/123/input', new UrlHelper(''))]
					],
					[
						'expected' => ['UrlParamController', 'input3', ['value' => '123']],
						'input' => [HttpMethod::Get, new RequestPath('controller/input/reg/123', new UrlHelper(''))]
					],
					[
						'expected' => null,
						'input' => [HttpMethod::Get, new RequestPath('controller/input/reg/abc', new UrlHelper(''))]
					],
					[
						'expected' => ['UrlParamController', 'input4', ['value1' => '123', 'value2' => '@@@', 'value3' => 'az']],
						'input' => [HttpMethod::Get, new RequestPath('controller/multi/123/@@@/az/none', new UrlHelper(''))]
					],
				]
			],
		];
		foreach ($tests as $test) {
			$route = new Route(...$test['route']);
			foreach ($test['actions'] as $action) {
				$route->addAction(...$action);
			}
			foreach ($test['request'] as $request) {
				$actual = $route->getAction(...$request['input']);
				$input = var_export($request['input'], true);
				if ($request['expected'] === null) {
					$this->assertSame(404, $actual->status->value, $input);
					$this->assertSame('', $actual->actionSetting->controllerMethod, $input);
				} else {
					$this->assertSame($request['expected'][0], $actual->className, $input);
					$this->assertSame($request['expected'][1], $actual->actionSetting->controllerMethod, $input);
					if (isset($request['expected'][2]) && $request['expected'][2] !== null) {
						foreach ($request['expected'][2] as $key => $value) {
							$this->assertSame($request['expected'][2][$key], $actual->params[$key], $input);
						}
					} else {
						$this->assertTrue(isset($actual->params));
					}
				}
			}
		}
	}
}
