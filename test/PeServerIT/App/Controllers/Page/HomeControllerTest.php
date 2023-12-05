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
use PeServer\Core\Throws\HttpStatusException;
use PeServerTest\ItLoginTrait;
use PeServerTest\MockStores;
use PeServerTest\TestControllerClass;
use Throwable;

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
		$this->assertStatusOk($actual);
		$this->assertTitle('トップ', $actual);
	}

	public function test_about()
	{
		$actual = $this->call(HttpMethod::Get, '/about');
		$this->assertStatusOk($actual);
		$this->assertTitle('問い合わせ', $actual);
	}

	public function test_privacy()
	{
		$actual = $this->call(HttpMethod::Get, '/about/privacy');
		$this->assertStatusOk($actual);
		$this->assertTitle('プライバシーポリシー', $actual);
	}

	public function test_contact()
	{
		$actual = $this->call(HttpMethod::Get, '/about/contact');
		$this->assertStatusOk($actual);
		$this->assertTitle('問い合わせ', $actual);
	}

	public function test_api_doc()
	{
		$actual = $this->call(HttpMethod::Get, '/api-doc');
		$this->assertStatusOk($actual);
		$this->assertTitle('API', $actual);
	}

	public function test_wildcard_favicon_ico()
	{
		$actual = $this->call(HttpMethod::Get, '/favicon.ico');
		$this->assertStatusOk($actual);
		$this->assertSame(Mime::ICON, $actual->getContentType()->mime);
	}

	public function test_wildcard_favicon_svg()
	{
		$actual = $this->call(HttpMethod::Get, '/favicon.svg');
		$this->assertStatusOk($actual);
		$this->assertSame(Mime::SVG, $actual->getContentType()->mime);
	}

	public function test_wildcard_robot()
	{
		$actual = $this->call(HttpMethod::Get, '/robot.txt');
		$this->assertStatusOk($actual);
		$this->assertSame(Mime::TEXT, $actual->getContentType()->mime);
	}

	public static function provider_wildcard_NotFound()
	{
		return [
			['/../../robot.txt'],
			['/<not found>'],
		];
	}

	/** @dataProvider provider_wildcard_NotFound */
	public function test_wildcard_NotFound(string $path)
	{
		try {
			$this->call(HttpMethod::Get, $path);
		} catch (HttpStatusException $ex) {
			$this->assertSame(HttpStatus::NotFound, $ex->status);
		}
	}
}
