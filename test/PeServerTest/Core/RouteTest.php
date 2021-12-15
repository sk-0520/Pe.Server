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
					['action', HttpMethod::get()]
				],
				'request' => [
					[
						'expected' => ['Controller', 'action'],
						'input' => [HttpMethod::GET, ['path', 'action']]
					]
				]
			],
			[
				'route' => ['api/test', 'TestController'],
				'actions' => [
					['list', HttpMethod::post()]
				],
				'request' => [
					[
						'expected' => ['TestController', 'list'],
						'input' => [HttpMethod::POST, ['api', 'test', 'list']]
					]
				]
			]

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
					$this->assertNull($actual, $input);
				} else {
					$this->assertEquals($request['expected'][0], $actual['class'], $input);
					$this->assertEquals($request['expected'][1], $actual['method'], $input);
				}
			}
		}
	}
}
