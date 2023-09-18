<?php

declare(strict_types=1);

namespace PeServerTest\Core\Web;

use PeServer\Core\Encoding;
use PeServer\Core\Throws\ParseException;
use PeServer\Core\Web\Url;
use PeServer\Core\Web\UrlPath;
use PeServer\Core\Web\UrlQuery;
use PeServer\Core\Web\UrlUtility;
use PeServerTest\Data;
use PeServerTest\TestClass;

class UrlTest extends TestClass
{
	public function test_try_parse_normal_success()
	{
		$this->assertTrue(Url::tryParse('http://user:pass@localhost:1234/path/child?query=value&key-only#fragment', $actual));
		$this->assertSame('http', $actual->scheme);
		$this->assertSame('user', $actual->user);
		$this->assertSame('pass', $actual->password);
		$this->assertSame(1234, $actual->port);
		$this->assertSame('/path/child', $actual->path->toString(false));
		$this->assertSame(['value'], $actual->query->getQuery()['query']);
		$this->assertSame([null], $actual->query->getQuery()['key-only']);
		$this->assertSame('fragment', $actual->fragment);
	}

	public function test_try_parse_normal_failure()
	{
		$this->assertFalse(Url::tryParse('', $_));
		$this->assertFalse(Url::tryParse(' ', $_));
		$this->assertFalse(Url::tryParse('*', $_));
		$this->assertFalse(Url::tryParse('http::://:80.localhost', $_));
		$this->assertFalse(Url::tryParse(' http://localhost ', $_));
	}

	public function test_try_parse_scheme()
	{
		$this->assertFalse(Url::tryParse('://host', $_));
		$this->assertFalse(Url::tryParse('//host', $_));
		$this->assertFalse(Url::tryParse('/host', $_));
		$this->assertFalse(Url::tryParse('host', $_));
	}

	public function test_try_parse_host()
	{
		$this->assertFalse(Url::tryParse('http://', $_));
		$this->assertFalse(Url::tryParse('http://:80', $_));
	}

	public function test_try_parse_user_only()
	{
		$this->assertTrue(Url::tryParse('http://user@localhost', $actual));
		$this->assertSame('http', $actual->scheme);
		$this->assertSame('user', $actual->user);
		$this->assertEmpty($actual->password);
		$this->assertSame('localhost', $actual->host);
	}

	public function test_try_parse_password_only()
	{
		$this->assertTrue(Url::tryParse('http://:pass@localhost', $actual));
		$this->assertSame('http', $actual->scheme);
		$this->assertEmpty($actual->user);
		$this->assertSame('pass', $actual->password);
		$this->assertSame('localhost', $actual->host);
	}

	public static function provider_parse_throw()
	{
		return [
			[''],
			[' '],
			[' http::://:80.localhost '],
			[' http://localhost '],
			['://host'],
			['//host'],
			['/host'],
			['host'],
			['http://'],
			['http://:80'],
		];
	}

	/** @dataProvider provider_parse_throw */
	public function test_parse_throw($input)
	{
		$this->expectException(ParseException::class);
		Url::parse($input);
		$this->fail();
	}

	public function test_parse_port()
	{
		$this->assertNull(Url::parse('http://localhost')->port);
		$this->assertNull(Url::parse('https://localhost')->port);
		$this->assertSame(443, Url::parse('http://localhost:443')->port);
		$this->assertSame(80, Url::parse('https://localhost:80')->port);
	}

	public function test_parse_decode()
	{
		//Url::parse('http://user🥚:pass🐣@localhost🐥:1234/path🐤/child?query🐓=value&key-only🐔#fragment🍗');
		$actual = Url::parse('http://user%F0%9F%A5%9A:pass%F0%9F%90%A3@localhost%F0%9F%90%A5:1234/path%F0%9F%90%A4/child?query%F0%9F%90%93=value&key-only%F0%9F%90%94#fragment%F0%9F%8D%97');
		$this->assertSame('user🥚', $actual->user);
		$this->assertSame('pass🐣', $actual->password);
		$this->assertSame('localhost%F0%9F%90%A5', $actual->host);
		$this->assertSame('/path%F0%9F%90%A4/child', $actual->path->toString(false));
		$this->assertSame(['value'], $actual->query->getQuery()['query🐓']);
		$this->assertSame([null], $actual->query->getQuery()['key-only🐔']);
		$this->assertSame('fragment🍗', $actual->fragment);
	}

	public function test_changeScheme()
	{
		$src = Url::parse('http://user:pass@localhost:1234/path/child?query=value&key-only#fragment');
		$new = $src->changeScheme('ftp');
		$this->assertSame('http', $src->scheme);
		$this->assertSame('ftp', $new->scheme);

		$this->assertNotSame($src->scheme, $new->scheme);
		$this->assertSame($src->user, $new->user);
		$this->assertSame($src->password, $new->password);
		$this->assertSame($src->host, $new->host);
		$this->assertSame($src->port, $new->port);
		$this->assertSame($src->path->getElements(), $new->path->getElements());
		$this->assertEqualsWithInfo('配列微妙', $src->query->getQuery(), $new->query->getQuery());
		$this->assertSame($src->fragment, $new->fragment);
	}

	public function test_changeAuthentication()
	{
		$src = Url::parse('http://user:pass@localhost:1234/path/child?query=value&key-only#fragment');
		$new = $src->changeAuthentication('USER', 'PASS');
		$this->assertSame('user', $src->user);
		$this->assertSame('USER', $new->user);
		$this->assertSame('pass', $src->password);
		$this->assertSame('PASS', $new->password);

		$this->assertSame($src->scheme, $new->scheme);
		$this->assertNotSame($src->user, $new->user);
		$this->assertNotSame($src->password, $new->password);
		$this->assertSame($src->host, $new->host);
		$this->assertSame($src->port, $new->port);
		$this->assertSame($src->path->getElements(), $new->path->getElements());
		$this->assertEqualsWithInfo('配列微妙', $src->query->getQuery(), $new->query->getQuery());
		$this->assertSame($src->fragment, $new->fragment);
	}

	public function test_clearAuthentication()
	{
		$src = Url::parse('http://user:pass@localhost:1234/path/child?query=value&key-only#fragment');
		$new = $src->clearAuthentication();
		$this->assertSame('user', $src->user);
		$this->assertSame('', $new->user);
		$this->assertSame('pass', $src->password);
		$this->assertSame('', $new->password);

		$this->assertSame($src->scheme, $new->scheme);
		$this->assertNotSame($src->user, $new->user);
		$this->assertNotSame($src->password, $new->password);
		$this->assertSame($src->host, $new->host);
		$this->assertSame($src->port, $new->port);
		$this->assertSame($src->path->getElements(), $new->path->getElements());
		$this->assertEqualsWithInfo('配列微妙', $src->query->getQuery(), $new->query->getQuery());
		$this->assertSame($src->fragment, $new->fragment);
	}

	public function test_changeHost()
	{
		$src = Url::parse('http://user:pass@localhost:1234/path/child?query=value&key-only#fragment');
		$new = $src->changeHost('127.0.0.1');
		$this->assertSame('localhost', $src->host);
		$this->assertSame('127.0.0.1', $new->host);

		$this->assertSame($src->scheme, $new->scheme);
		$this->assertSame($src->user, $new->user);
		$this->assertSame($src->password, $new->password);
		$this->assertNotSame($src->host, $new->host);
		$this->assertSame($src->port, $new->port);
		$this->assertSame($src->path->getElements(), $new->path->getElements());
		$this->assertEqualsWithInfo('配列微妙', $src->query->getQuery(), $new->query->getQuery());
		$this->assertSame($src->fragment, $new->fragment);
	}

	public function test_changePort()
	{
		$src = Url::parse('http://user:pass@localhost:1234/path/child?query=value&key-only#fragment');
		$new = $src->changePort(null);
		$this->assertSame(1234, $src->port);
		$this->assertSame(null, $new->port);

		$this->assertSame($src->scheme, $new->scheme);
		$this->assertSame($src->user, $new->user);
		$this->assertSame($src->password, $new->password);
		$this->assertSame($src->host, $new->host);
		$this->assertNotSame($src->port, $new->port);
		$this->assertSame($src->path->getElements(), $new->path->getElements());
		$this->assertEqualsWithInfo('配列微妙', $src->query->getQuery(), $new->query->getQuery());
		$this->assertSame($src->fragment, $new->fragment);
	}

	public function test_changePath()
	{
		$src = Url::parse('http://user:pass@localhost:1234/path/child?query=value&key-only#fragment');
		$new = $src->changePath(new UrlPath("/a/b/c"));
		$this->assertSame(['path', 'child'], $src->path->getElements());
		$this->assertSame(['a', 'b', 'c'], $new->path->getElements());

		$this->assertSame($src->scheme, $new->scheme);
		$this->assertSame($src->user, $new->user);
		$this->assertSame($src->password, $new->password);
		$this->assertSame($src->host, $new->host);
		$this->assertSame($src->port, $new->port);
		$this->assertNotSame($src->path->getElements(), $new->path->getElements());
		$this->assertEqualsWithInfo('配列微妙', $src->query->getQuery(), $new->query->getQuery());
		$this->assertSame($src->fragment, $new->fragment);
	}

	public function test_changeQuery()
	{
		$src = Url::parse('http://user:pass@localhost:1234/path/child?query=value&key-only#fragment');
		$new = $src->changeQuery(new UrlQuery("Q=K&Q&"));
		$this->assertEqualsWithInfo('配列微妙', ['query' => ['value'], 'key-only' => [null]], $src->query->getQuery());
		$this->assertEqualsWithInfo('配列微妙', ['Q' => ['K', null]], $new->query->getQuery());

		$this->assertSame($src->scheme, $new->scheme);
		$this->assertSame($src->user, $new->user);
		$this->assertSame($src->password, $new->password);
		$this->assertSame($src->host, $new->host);
		$this->assertSame($src->port, $new->port);
		$this->assertSame($src->path->getElements(), $new->path->getElements());
		$this->assertNotEqualsWithInfo('配列微妙', $src->query->getQuery(), $new->query->getQuery());
		$this->assertSame($src->fragment, $new->fragment);
	}

	public function test_changeFragment()
	{
		$src = Url::parse('http://user:pass@localhost:1234/path/child?query=value&key-only#fragment');
		$new = $src->changeFragment(null);
		$this->assertSame('fragment', $src->fragment);
		$this->assertSame(null, $new->fragment);

		$this->assertSame($src->scheme, $new->scheme);
		$this->assertSame($src->user, $new->user);
		$this->assertSame($src->password, $new->password);
		$this->assertSame($src->host, $new->host);
		$this->assertSame($src->port, $new->port);
		$this->assertSame($src->path->getElements(), $new->path->getElements());
		$this->assertEqualsWithInfo('配列微妙', $src->query->getQuery(), $new->query->getQuery());
		$this->assertNotSame($src->fragment, $new->fragment);
	}

	public function test_toString() {
		$tests = [
			new Data('http://localhost', 'http://localhost', false),
			new Data('http://USER@localhost', 'http://USER@localhost', false),
			new Data('http://:PASS@localhost', 'http://:PASS@localhost', false),
			new Data('http://USER:PASS@localhost', 'http://USER:PASS@localhost', false),
			new Data('http://localhost:8888', 'http://localhost:8888', false),
		];
		foreach ($tests as $test) {
			$actual = Url::parse($test->args[0]);
			$this->assertSame($test->expected, $actual->toString(null, $test->args[1]), $test->str());
		}
	}
}
