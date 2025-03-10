<?php

declare(strict_types=1);

namespace PeServerUT\Core\Http\Client;

use PeServer\Core\Binary;
use PeServer\Core\Collections\Dictionary;
use PeServer\Core\Encoding;
use PeServer\Core\Http\Client\BinaryContent;
use PeServer\Core\Http\Client\FormUrlEncodedContent;
use PeServer\Core\Http\Client\HttpClient;
use PeServer\Core\Http\Client\HttpClientOptions;
use PeServer\Core\Http\Client\HttpClientRequest;
use PeServer\Core\Http\Client\HttpRedirectOptions;
use PeServer\Core\Http\Client\JsonContent;
use PeServer\Core\Http\Client\StringContent;
use PeServer\Core\Http\ContentType;
use PeServer\Core\Http\HttpHeader;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mime;
use PeServer\Core\Serialization\JsonSerializer;
use PeServer\Core\Text;
use PeServer\Core\Throws\HttpClientRequestException;
use PeServer\Core\Throws\HttpClientStatusException;
use PeServer\Core\Web\Url;
use PeServer\Core\Web\UrlQuery;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

/**
 */
#[Group('slow')]
class HttpClientTest extends TestClass
{
	public function test_get_path_echo()
	{
		$url = Url::parse(self::localServer('/Core/Http/Client/get-path-echo.php'));

		$hc = new HttpClient(new HttpClientOptions());
		$actual1 = $hc->get($url);
		$this->assertSame('/Core/Http/Client/get-path-echo.php', $actual1->content->raw);
		$actual1->dispose();

		$actual2 = $hc->send(new HttpClientRequest($url, HttpMethod::Get, null, null));
		$this->assertSame('/Core/Http/Client/get-path-echo.php', $actual2->content->raw);
		$actual2->dispose();

		$actual3 = HttpClient::request(new HttpClientRequest($url, HttpMethod::Get, null, null), new HttpClientOptions());
		$this->assertSame('/Core/Http/Client/get-path-echo.php', $actual3->content->raw);
		$actual3->dispose();
	}

	public static function provider_get_path_echo_405_throw()
	{
		return [
			//[HttpMethod::Get],
			[HttpMethod::Head],
			[HttpMethod::Post],
			[HttpMethod::Put],
			[HttpMethod::Delete],
			[HttpMethod::Connect],
			[HttpMethod::Options],
			[HttpMethod::Trace],
			[HttpMethod::Patch],
		];
	}

	#[DataProvider('provider_get_path_echo_405_throw')]
	public function test_get_path_echo_405_throw(HttpMethod $method)
	{
		$url = Url::parse(self::localServer('/Core/Http/Client/get-path-echo.php'));

		$hc = new HttpClient(new HttpClientOptions());

		$this->expectException(HttpClientRequestException::class);
		$hc->send(new HttpClientRequest($url, $method, null, null));
		$this->fail();
	}

	public function test_post_data_echo()
	{
		$url = Url::parse(self::localServer('/Core/Http/Client/post-data-echo.php'));

		$hc = new HttpClient(new HttpClientOptions());

		$header1 = HttpHeader::createClientRequestHeader();
		$header1->setContentType(ContentType::create('application/octet-stream', null));
		$actual1 = $hc->post($url, $header1, null);
		$this->assertSame('application/octet-stream', $actual1->header->getContentType()->mime);
		$this->assertSame('', $actual1->content->raw);
		$actual1->dispose();

		$actual2 = $hc->post($url, null, new BinaryContent(new Binary("あ\0い\1う\2え\3お！\4"), 'text/binary-text'));
		$this->assertSame('text/binary-text', $actual2->header->getContentType()->mime);
		$this->assertSame("あ\0い\1う\2え\3お！\4", $actual2->content->raw);
		$actual2->dispose();

		$actual3 = $hc->post($url, null, new StringContent('かきくけこ？', 'text/plain-text'));
		$this->assertSame('text/plain-text', $actual3->header->getContentType()->mime);
		$this->assertSame('かきくけこ？', $actual3->content->raw);
		$actual3->dispose();

		$actual4 = $hc->post($url, null, new JsonContent(["a" => "A", "b" => [1, 2, 3]]));
		$this->assertSame(Mime::JSON, $actual4->header->getContentType()->mime);
		$this->assertSame(["a" => "A", "b" => [1, 2, 3]], (new JsonSerializer())->load($actual4->content));
		$actual4->dispose();

		$actual5 = HttpClient::request(new HttpClientRequest($url, HttpMethod::Post, null, new StringContent('さしすせそ！', 'mime/💩')), new HttpClientOptions());
		$this->assertSame('mime/💩', $actual5->header->getContentType()->mime);
		$this->assertSame("さしすせそ！", $actual5->content->raw);
		$actual5->dispose();

		$actual6 = HttpClient::request(new HttpClientRequest($url, HttpMethod::Post, null, new FormUrlEncodedContent(Dictionary::create(['KEY' => 'あ']))), new HttpClientOptions());
		$this->assertSame(Mime::FORM, $actual6->header->getContentType()->mime);
		$this->assertSame("KEY=%E3%81%82", $actual6->content->raw);

		//TODO: あとはマルチパートくらいだろうけどもう別にどうでもいいわ
	}

	public static function provider_post_path_echo_405_throw()
	{
		return [
			[HttpMethod::Get],
			[HttpMethod::Head],
			//[HttpMethod::Post],
			[HttpMethod::Put],
			[HttpMethod::Delete],
			[HttpMethod::Connect],
			[HttpMethod::Options],
			[HttpMethod::Trace],
			[HttpMethod::Patch],
		];
	}

	#[DataProvider('provider_post_path_echo_405_throw')]
	public function test_post_data_echo_405_throw(HttpMethod $method)
	{
		$url = Url::parse(self::localServer('/Core/Http/Client/post-data-echo.php'));

		$hc = new HttpClient(new HttpClientOptions());

		$this->expectException(HttpClientRequestException::class);
		$header = HttpHeader::createClientRequestHeader();
		$header->setContentType(ContentType::create('application/octet-stream'));
		$hc->send(new HttpClientRequest($url, $method, $header, null));
		$this->fail();
	}

	public function test_put_data_echo()
	{
		$url = Url::parse(self::localServer('/Core/Http/Client/put-data-echo.php'));

		$hc = new HttpClient(new HttpClientOptions());

		$header1 = HttpHeader::createClientRequestHeader();
		$header1->setContentType(ContentType::create('application/octet-stream', null));
		$actual1 = $hc->put($url, $header1, null);
		$this->assertSame('application/octet-stream', $actual1->header->getContentType()->mime);
		$this->assertSame('', $actual1->content->raw);
		$actual1->dispose();

		$actual2 = $hc->put($url, null, new BinaryContent(new Binary("あ\0い\1う\2え\3お！\4"), 'text/binary-text'));
		$this->assertSame('text/binary-text', $actual2->header->getContentType()->mime);
		$this->assertSame("あ\0い\1う\2え\3お！\4", $actual2->content->raw);
		$actual2->dispose();

		$actual3 = $hc->put($url, null, new StringContent('かきくけこ？', 'text/plain-text'));
		$this->assertSame('text/plain-text', $actual3->header->getContentType()->mime);
		$this->assertSame('かきくけこ？', $actual3->content->raw);
		$actual3->dispose();

		$actual4 = $hc->put($url, null, new JsonContent(["a" => "A", "b" => [1, 2, 3]]));
		$this->assertSame(Mime::JSON, $actual4->header->getContentType()->mime);
		$this->assertSame(["a" => "A", "b" => [1, 2, 3]], (new JsonSerializer())->load($actual4->content));
		$actual4->dispose();

		$actual5 = HttpClient::request(new HttpClientRequest($url, HttpMethod::Put, null, new StringContent('さしすせそ！', 'mime/💩')), new HttpClientOptions());
		$this->assertSame('mime/💩', $actual5->header->getContentType()->mime);
		$this->assertSame("さしすせそ！", $actual5->content->raw);
		$actual5->dispose();

		$actual6 = HttpClient::request(new HttpClientRequest($url, HttpMethod::Put, null, new FormUrlEncodedContent(Dictionary::create(['KEY' => 'あ']))), new HttpClientOptions());
		$this->assertSame(Mime::FORM, $actual6->header->getContentType()->mime);
		$this->assertSame("KEY=%E3%81%82", $actual6->content->raw);
	}

	public static function provider_put_path_echo_405_throw()
	{
		return [
			[HttpMethod::Get],
			[HttpMethod::Head],
			[HttpMethod::Post],
			//[HttpMethod::Put],
			[HttpMethod::Delete],
			[HttpMethod::Connect],
			[HttpMethod::Options],
			[HttpMethod::Trace],
			[HttpMethod::Patch],
		];
	}

	#[DataProvider('provider_put_path_echo_405_throw')]
	public function test_put_data_echo_405_throw(HttpMethod $method)
	{
		$url = Url::parse(self::localServer('/Core/Http/Client/put-data-echo.php'));

		$hc = new HttpClient(new HttpClientOptions());

		$this->expectException(HttpClientRequestException::class);
		$header = HttpHeader::createClientRequestHeader();
		$header->setContentType(ContentType::create('application/octet-stream'));
		$hc->send(new HttpClientRequest($url, $method, $header, null));
		$this->fail();
	}

	public function test_patch_data_echo()
	{
		$url = Url::parse(self::localServer('/Core/Http/Client/patch-data-echo.php'));

		$hc = new HttpClient(new HttpClientOptions());

		$header1 = HttpHeader::createClientRequestHeader();
		$header1->setContentType(ContentType::create('application/octet-stream', null));
		$actual1 = $hc->patch($url, $header1, null);
		$this->assertSame('application/octet-stream', $actual1->header->getContentType()->mime);
		$this->assertSame('', $actual1->content->raw);
		$actual1->dispose();

		$actual2 = $hc->patch($url, null, new BinaryContent(new Binary("あ\0い\1う\2え\3お！\4"), 'text/binary-text'));
		$this->assertSame('text/binary-text', $actual2->header->getContentType()->mime);
		$this->assertSame("あ\0い\1う\2え\3お！\4", $actual2->content->raw);
		$actual2->dispose();

		$actual3 = $hc->patch($url, null, new StringContent('かきくけこ？', 'text/plain-text'));
		$this->assertSame('text/plain-text', $actual3->header->getContentType()->mime);
		$this->assertSame('かきくけこ？', $actual3->content->raw);
		$actual3->dispose();

		$actual4 = $hc->patch($url, null, new JsonContent(["a" => "A", "b" => [1, 2, 3]]));
		$this->assertSame(Mime::JSON, $actual4->header->getContentType()->mime);
		$this->assertSame(["a" => "A", "b" => [1, 2, 3]], (new JsonSerializer())->load($actual4->content));
		$actual4->dispose();

		$actual5 = HttpClient::request(new HttpClientRequest($url, HttpMethod::Patch, null, new StringContent('さしすせそ！', 'mime/💩')), new HttpClientOptions());
		$this->assertSame('mime/💩', $actual5->header->getContentType()->mime);
		$this->assertSame("さしすせそ！", $actual5->content->raw);
		$actual5->dispose();

		$actual6 = HttpClient::request(new HttpClientRequest($url, HttpMethod::Patch, null, new FormUrlEncodedContent(Dictionary::create(['KEY' => 'あ']))), new HttpClientOptions());
		$this->assertSame(Mime::FORM, $actual6->header->getContentType()->mime);
		$this->assertSame("KEY=%E3%81%82", $actual6->content->raw);
	}

	public static function provider_patch_path_echo_405_throw()
	{
		return [
			[HttpMethod::Get],
			[HttpMethod::Head],
			[HttpMethod::Post],
			[HttpMethod::Put],
			[HttpMethod::Delete],
			[HttpMethod::Connect],
			[HttpMethod::Options],
			[HttpMethod::Trace],
			//[HttpMethod::Patch],
		];
	}

	#[DataProvider('provider_patch_path_echo_405_throw')]
	public function test_patch_data_echo_405_throw(HttpMethod $method)
	{
		$url = Url::parse(self::localServer('/Core/Http/Client/patch-data-echo.php'));

		$hc = new HttpClient(new HttpClientOptions());

		$this->expectException(HttpClientRequestException::class);
		$header = HttpHeader::createClientRequestHeader();
		$header->setContentType(ContentType::create('application/octet-stream'));
		$hc->send(new HttpClientRequest($url, $method, $header, null));
		$this->fail();
	}

	public function test_delete_data_echo()
	{
		$url = Url::parse(self::localServer('/Core/Http/Client/delete-data-echo.php'));

		$hc = new HttpClient(new HttpClientOptions());

		$header1 = HttpHeader::createClientRequestHeader();
		$header1->setContentType(ContentType::create('application/octet-stream', null));
		$actual1 = $hc->delete($url, $header1, null);
		$this->assertSame('application/octet-stream', $actual1->header->getContentType()->mime);
		$this->assertSame('', $actual1->content->raw);
		$actual1->dispose();

		$actual2 = $hc->delete($url, null, new BinaryContent(new Binary("あ\0い\1う\2え\3お！\4"), 'text/binary-text'));
		$this->assertSame('text/binary-text', $actual2->header->getContentType()->mime);
		$this->assertSame("あ\0い\1う\2え\3お！\4", $actual2->content->raw);
		$actual2->dispose();

		$actual3 = $hc->delete($url, null, new StringContent('かきくけこ？', 'text/plain-text'));
		$this->assertSame('text/plain-text', $actual3->header->getContentType()->mime);
		$this->assertSame('かきくけこ？', $actual3->content->raw);
		$actual3->dispose();

		$actual4 = $hc->delete($url, null, new JsonContent(["a" => "A", "b" => [1, 2, 3]]));
		$this->assertSame(Mime::JSON, $actual4->header->getContentType()->mime);
		$this->assertSame(["a" => "A", "b" => [1, 2, 3]], (new JsonSerializer())->load($actual4->content));
		$actual4->dispose();

		$actual5 = HttpClient::request(new HttpClientRequest($url, HttpMethod::Delete, null, new StringContent('さしすせそ！', 'mime/💩')), new HttpClientOptions());
		$this->assertSame('mime/💩', $actual5->header->getContentType()->mime);
		$this->assertSame("さしすせそ！", $actual5->content->raw);
		$actual5->dispose();

		$actual6 = HttpClient::request(new HttpClientRequest($url, HttpMethod::Delete, null, new FormUrlEncodedContent(Dictionary::create(['KEY' => 'あ']))), new HttpClientOptions());
		$this->assertSame(Mime::FORM, $actual6->header->getContentType()->mime);
		$this->assertSame("KEY=%E3%81%82", $actual6->content->raw);
	}

	public static function provider_delete_path_echo_405_throw()
	{
		return [
			[HttpMethod::Get],
			[HttpMethod::Head],
			[HttpMethod::Post],
			[HttpMethod::Put],
			//[HttpMethod::Delete],
			[HttpMethod::Connect],
			[HttpMethod::Options],
			[HttpMethod::Trace],
			[HttpMethod::Patch],
		];
	}

	#[DataProvider('provider_delete_path_echo_405_throw')]
	public function test_delete_data_echo_405_throw(HttpMethod $method)
	{
		$url = Url::parse(self::localServer('/Core/Http/Client/delete-data-echo.php'));

		$hc = new HttpClient(new HttpClientOptions());

		$this->expectException(HttpClientRequestException::class);
		$header = HttpHeader::createClientRequestHeader();
		$header->setContentType(ContentType::create('application/octet-stream'));
		$hc->send(new HttpClientRequest($url, $method, $header, null));
		$this->fail();
	}

	public function test_redirect_success()
	{
		$redirectCount = 3;
		$redirectMaxCount = 3;
		$url = Url::parse(self::localServer('/Core/Http/Client/redirect-loop.php'))->changeQuery(UrlQuery::from(['redirect' => $redirectCount]));

		$hc = new HttpClient(new HttpClientOptions(redirect: new HttpRedirectOptions(count: $redirectMaxCount)));
		$actual = $hc->get($url);

		$this->assertSame('GOAL!', $actual->content->raw);
		$this->assertSame($redirectCount, $actual->information->getRedirectCount());
		$this->assertSame($url->changeQuery(UrlQuery::from(['redirect' => 0]))->toString(), $actual->information->getEffectiveUrl()->toString());
	}

	public function test_redirect_failure_max()
	{
		$redirectCount = 3;
		$redirectMaxCount = 2;
		$url = Url::parse(self::localServer('/Core/Http/Client/redirect-loop.php'))->changeQuery(UrlQuery::from(['redirect' => $redirectCount]));

		$hc = new HttpClient(new HttpClientOptions(redirect: new HttpRedirectOptions(count: $redirectMaxCount)));

		try {
			$hc->get($url);
			$this->fail();
		} catch (HttpClientStatusException $ex) {
			$this->assertSame(CURLE_TOO_MANY_REDIRECTS, $ex->response->clientStatus->number);
			$this->assertSame(HttpStatus::Found->value, $ex->response->information->getHttpStatus()->value);
			$this->assertSame('', $ex->response->content->raw);
		}
	}

	public function test_redirect_failure_no_redirect()
	{
		$redirectCount = 3;
		$url = Url::parse(self::localServer('/Core/Http/Client/redirect-loop.php'))->changeQuery(UrlQuery::from(['redirect' => $redirectCount]));

		$hc = new HttpClient(new HttpClientOptions(redirect: new HttpRedirectOptions(isEnabled: false)));

		$actual = $hc->get($url);
		$this->assertSame(HttpStatus::Found->value, $actual->information->getHttpStatus()->value);
		$this->assertTrue($actual->information->getHttpStatus()->isRedirect());
		$this->assertSame('still', $actual->content->raw);
	}
}
