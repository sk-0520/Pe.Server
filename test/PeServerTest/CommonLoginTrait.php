<?php

declare(strict_types=1);

namespace PeServerTest;

use PeServer\App\Models\Domain\UserLevel;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\HttpStatus;

trait CommonLoginTrait
{
	private static function _provider_notLogin(array $path)
	{
		return [$path];
	}

	private static function _provider_login(array $path, $levels = [UserLevel::USER, UserLevel::ADMINISTRATOR])
	{
		$result = [];

		foreach ($path as $p) {
			foreach ($levels as $l) {
				$result[] = [$p, $l];
			}
		}

		return $result;
	}

	private function _test_notLogin(string $path)
	{
		$actual = $this->call(HttpMethod::Get, $path);
		$this->assertSame(HttpStatus::OK, $actual->getHttpStatus());
		$this->assertTrue($actual->isHtml());

		$this->assertCount(1, $actual->html->path()->collection('//header//li/a[@href = "/account/signup"]'));
		$this->assertCount(0, $actual->html->path()->collection('//header//li/a[@href = "/account/user"]'));
	}

	private function _test_login(string $path, string $userLevel)
	{
		$actual = $this->call(HttpMethod::Get, $path, MockStores::account($userLevel));
		$this->assertSame(HttpStatus::OK, $actual->getHttpStatus());
		$this->assertTrue($actual->isHtml());

		$this->assertCount(0, $actual->html->path()->collection('//header//li/a[@href = "/account/signup"]'));
		$this->assertCount(1, $actual->html->path()->collection('//header//li/a[@href = "/account/user"]'));
	}
}
