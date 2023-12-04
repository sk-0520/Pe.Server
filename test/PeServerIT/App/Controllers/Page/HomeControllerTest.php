<?php

declare(strict_types=1);

namespace PeServerIT\App\Controllers\Page;

use PeServer\App\Controllers\Page\HomeController;
use PeServer\App\Models\Data\SessionAccount;
use PeServer\App\Models\Domain\UserLevel;
use PeServer\App\Models\Domain\UserState;
use PeServer\Core\Collections\Collection;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mime;
use PeServerTest\ItLoginTrait;
use PeServerTest\MockStores;
use PeServerTest\TestControllerClass;

class HomeControllerTest extends TestControllerClass
{
	#region common logion

	use ItLoginTrait;

	public static function provider_it_notLogin()
	{
		return self::_provider_it_notLogin([
			'/',
			'/about',
			'/about/privacy',
			'/about/contact',
			'/favicon.ico',
			'/robot.txt',
		]);
	}

	public static function provider_it_login()
	{
		return self::_provider_it_login([
			'/',
			'/about',
			'/about/privacy',
			'/about/contact',
		]);
	}

	/** @dataProvider provider_it_notLogin */
	public function test_it_notLogin(string $path)
	{
		$this->_test_notLogin($path);
	}

	/** @dataProvider provider_it_login */
	public function test_it_login(string $path, string $level)
	{
		$this->_test_login($path, $level);
	}

	#endregion

	public function test_index()
	{
		$actual = $this->call(HttpMethod::Get, '');
		$this->assertSame(HttpStatus::OK, $actual->getHttpStatus());
		$this->assertTitle('トップ', $actual);

		$this->assertStatus(HttpStatus::OK, HttpMethod::Get, '/');
	}

	public function test_about()
	{
		$actual = $this->call(HttpMethod::Get, '/about');
		$this->assertSame(HttpStatus::OK, $actual->getHttpStatus());
		$this->assertTitle('問い合わせ', $actual);
	}

	public function test_privacy()
	{
		$actual = $this->call(HttpMethod::Get, '/about/privacy');
		$this->assertSame(HttpStatus::OK, $actual->getHttpStatus());
		$this->assertTitle('プライバシーポリシー', $actual);
	}

	public function test_contact()
	{
		$actual = $this->call(HttpMethod::Get, '/about/contact');
		$this->assertSame(HttpStatus::OK, $actual->getHttpStatus());
		$this->assertTitle('問い合わせ', $actual);
	}

	public function test_api_doc()
	{
		$actual = $this->call(HttpMethod::Get, '/api-doc');
		$this->assertSame(HttpStatus::OK, $actual->getHttpStatus());
		$this->assertTitle('API', $actual);
	}

	public function test_wildcard_favicon_ico()
	{
		$actual = $this->call(HttpMethod::Get, '/favicon.ico');
		$this->assertSame(HttpStatus::OK, $actual->getHttpStatus());
		$this->assertSame(Mime::ICON, $actual->getContentType()->mime);
	}

	public function test_wildcard_favicon_svg()
	{
		$actual = $this->call(HttpMethod::Get, '/favicon.svg');
		$this->assertSame(HttpStatus::OK, $actual->getHttpStatus());
		$this->assertSame(Mime::SVG, $actual->getContentType()->mime);
	}

	public function test_wildcard_robot()
	{
		$actual = $this->call(HttpMethod::Get, '/robot.txt');
		$this->assertSame(HttpStatus::OK, $actual->getHttpStatus());
		$this->assertSame(Mime::TEXT, $actual->getContentType()->mime);
	}

	public function test_wildcard_dot()
	{
		$this->assertStatus(HttpStatus::NotFound, HttpMethod::Get, '/../../robot.txt');
	}

	public function test_not_found()
	{
		$this->assertStatus(HttpStatus::NotFound, HttpMethod::Get, '/<not found>');
	}
}
