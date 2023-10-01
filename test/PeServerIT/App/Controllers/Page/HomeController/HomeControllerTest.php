<?php

declare(strict_types=1);

namespace PeServerIT\App\Controllers\Page;

use PeServer\App\Controllers\Page\HomeController;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mime;
use PeServerTest\TestControllerClass;

class HomeControllerTest extends TestControllerClass
{
	public function test_index()
	{
		$actual = $this->call(HttpMethod::Get, '');
		$this->assertSame(HttpStatus::OK, $actual->getHttpStatus());
		$this->assertTrue($actual->isHtml());
		$this->assertSame('トップ - Peサーバー', $actual->html->getTitle());

		$this->assertStatus(HttpStatus::OK, HttpMethod::Get, '/');
	}

	public function test_about()
	{
		$actual = $this->call(HttpMethod::Get, '/about');
		$this->assertSame(HttpStatus::OK, $actual->getHttpStatus());
		$this->assertTrue($actual->isHtml());
		$this->assertSame('問い合わせ - Peサーバー', $actual->html->getTitle());
	}

	public function test_privacy()
	{
		$actual = $this->call(HttpMethod::Get, '/about/privacy');
		$this->assertSame(HttpStatus::OK, $actual->getHttpStatus());
		$this->assertTrue($actual->isHtml());
		$this->assertSame('プライバシーポリシー - Peサーバー', $actual->html->getTitle());
	}

	public function test_contact()
	{
		$actual = $this->call(HttpMethod::Get, '/about/contact');
		$this->assertSame(HttpStatus::OK, $actual->getHttpStatus());
		$this->assertTrue($actual->isHtml());
		$this->assertSame('問い合わせ - Peサーバー', $actual->html->getTitle());
	}

	public function test_api_doc()
	{
		$actual = $this->call(HttpMethod::Get, '/api-doc');
		$this->assertSame(HttpStatus::OK, $actual->getHttpStatus());
		$this->assertTrue($actual->isHtml());
		$this->assertSame('API - Peサーバー', $actual->html->getTitle());
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

	public function test_wildcard_dt()
	{
		$this->assertStatus(HttpStatus::NotFound, HttpMethod::Get, '/../../robot.txt');
	}

	public function test_not_found()
	{
		$this->assertStatus(HttpStatus::NotFound, HttpMethod::Get, '/not');
	}
}