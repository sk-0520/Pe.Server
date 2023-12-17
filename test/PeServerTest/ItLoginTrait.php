<?php

declare(strict_types=1);

namespace PeServerTest;

use PeServer\App\Models\Domain\UserLevel;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\HttpStatus;

trait ItLoginTrait
{
	//phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps, PSR2.Methods.MethodDeclaration.Underscore
	private static function _provider_it_notLogin(array $path)
	{
		return [$path];
	}

	//phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps, PSR2.Methods.MethodDeclaration.Underscore
	private static function _provider_it_login(array $path, $levels = [UserLevel::USER, UserLevel::ADMINISTRATOR])
	{
		$result = [];

		foreach ($path as $p) {
			foreach ($levels as $l) {
				$result[] = [$p, $l];
			}
		}

		return $result;
	}

	//phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps, PSR2.Methods.MethodDeclaration.Underscore
	private function _test_notLogin(string $path)
	{
		$actual = $this->call(HttpMethod::Get, $path);
		$this->assertSame(HttpStatus::OK, $actual->getHttpStatus());
		$this->assertTrue($actual->isHtml());

		$this->assertCount(1, $actual->html->path()->collections('//header//li/a[@href = "/account/signup"]'));
		$this->assertCount(0, $actual->html->path()->collections('//header//li/a[@href = "/account/user"]'));
	}

	//phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps, PSR2.Methods.MethodDeclaration.Underscore
	private function _test_login(string $path, string $userLevel)
	{
		$options = new ItOptions(
			stores: ItMockStores::account($userLevel)
		);
		$actual = $this->call(HttpMethod::Get, $path, $options);
		$this->assertSame(HttpStatus::OK, $actual->getHttpStatus());
		$this->assertTrue($actual->isHtml());

		$this->assertCount(0, $actual->html->path()->collections('//header//li/a[@href = "/account/signup"]'));
		$this->assertCount(1, $actual->html->path()->collections('//header//li/a[@href = "/account/user"]'));
	}
}
