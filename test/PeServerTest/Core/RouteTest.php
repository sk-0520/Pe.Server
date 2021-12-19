<?php

declare(strict_types=1);

namespace PeServerTest\Core;

use \LogicException;
use \PeServer\App\Models\RouteConfiguration;
use \PeServer\Core\HttpMethod;
use \PeServerTest\Data;
use \PeServerTest\TestClass;
use \PeServer\Core\Route;

class RouteTest extends TestClass
{
	public function test_construct_exception_slash()
	{
		$this->expectException(LogicException::class);
		new Route('/', 'ClassName');
	}

	public function test_construct_exception_start()
	{
		$this->expectException(LogicException::class);
		new Route('/root', 'ClassName');
	}

	public function test_construct_exception_end()
	{
		$this->expectException(LogicException::class);
		new Route('root/', 'ClassName');
	}

	public function test_getAction()
	{
		$tests = [
			[
				'route' => ['path', 'Controller'],
				'actions' => [
					['action', HttpMethod::get(), "1"],
					['action/action', HttpMethod::get(), "2"],
				],
				'request' => [
					[
						'expected' => ['Controller', '1'],
						'input' => [HttpMethod::GET, ['path', 'action']]
					],
					[
						'expected' => ['Controller', '2'],
						'input' => [HttpMethod::GET, ['path', 'action', 'action']]
					],
				]
			],
			[
				'route' => ['api/test', 'TestController'],
				'actions' => [
					['list', HttpMethod::post()],
				],
				'request' => [
					[
						'expected' => ['TestController', 'list'],
						'input' => [HttpMethod::POST, ['api', 'test', 'list']]
					],
				]
			],
			[
				'route' => ['controller', 'UrlParamController'],
				'actions' => [
					['input/:value', HttpMethod::get(), 'input1'],
					[':value/input', HttpMethod::get(), 'input2'],
					['input/reg/:value@\\d+', HttpMethod::get(), 'input3'],
					['multi/:value1@\\d+/:value2/:value3@[a-z]+/none', HttpMethod::get(), 'input4'],
				],
				'request' => [
					[
						'expected' => ['UrlParamController', 'input1', ['value' => '123']],
						'input' => [HttpMethod::GET, ['controller', 'input', '123']]
					],
					[
						'expected' => ['UrlParamController', 'input2', ['value' => '123']],
						'input' => [HttpMethod::GET, ['controller', '123', 'input']]
					],
					[
						'expected' => ['UrlParamController', 'input3', ['value' => '123']],
						'input' => [HttpMethod::GET, ['controller', 'input', 'reg', '123']]
					],
					[
						'expected' => null,
						'input' => [HttpMethod::GET, ['controller', 'input', 'reg', 'abc']]
					],
					[
						'expected' => ['UrlParamController', 'input4', ['value1' => '123', 'value2' => '@@@', 'value3' => 'az']],
						'input' => [HttpMethod::GET, ['controller', 'multi', '123', '@@@', 'az', 'none']]
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
				if (is_null($request['expected'])) {
					$this->assertEquals(404, $actual['code'], $input);
					$this->assertEquals('', $actual['method'], $input);
				} else {
					$this->assertEquals($request['expected'][0], $actual['class'], $input);
					$this->assertEquals($request['expected'][1], $actual['method'], $input);
					if (isset($request['expected'][2]) && !is_null($request['expected'][2])) {
						foreach ($request['expected'][2] as $key => $value) {
							$this->assertEquals($request['expected'][2][$key], $actual['params'][$key], $input);
						}
					} else {
						$this->assertNull($actual['params']);
					}
				}
			}
		}
	}
}
