<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template;

use ArrayAccess;
use PeServer\Core\Store\CookieStore;
use PeServer\Core\Store\SessionStore;
use PeServer\Core\Store\TemporaryStore;
use PeServer\Core\Throws\NotImplementedException;

abstract class TemplateStore implements ArrayAccess //@phpstan-ignore-line
{
	#region function

	public static function createCookie(CookieStore $store): TemplateStore
	{
		return new LocalTemplateCookieStore($store);
	}

	public static function createSession(SessionStore $store): TemplateStore
	{
		return new LocalTemplateSessionStore($store);
	}

	public static function createTemporary(TemporaryStore $store): TemplateStore
	{
		return new LocalTemplateTemporaryStore($store);
	}

	abstract protected function get(string $name): mixed;

	#endregion

	#region ArrayAccess

	public function offsetExists(mixed $offset): bool
	{
		throw new NotImplementedException();
	}
	public function offsetGet(mixed $offset): mixed
	{
		return $this->get($offset);
	}
	public function offsetSet(mixed $offset, mixed $value): void
	{
		throw new NotImplementedException();
	}
	public function offsetUnset(mixed $offset): void
	{
		throw new NotImplementedException();
	}

	#endregion
}

//phpcs:ignore PSR1.Classes.ClassDeclaration.MultipleClasses
final class LocalTemplateCookieStore extends TemplateStore
{
	public function __construct(
		private CookieStore $store
	) {
	}

	protected function get(string $name): mixed
	{
		if ($this->store->tryGet($name, $result)) {
			return $result;
		}

		return null;
	}
}

//phpcs:ignore PSR1.Classes.ClassDeclaration.MultipleClasses
final class LocalTemplateSessionStore extends TemplateStore
{
	public function __construct(
		private SessionStore $store
	) {
	}

	protected function get(string $name): mixed
	{
		if ($this->store->tryGet($name, $result)) {
			return $result;
		}

		return null;
	}
}

//phpcs:ignore PSR1.Classes.ClassDeclaration.MultipleClasses
final class LocalTemplateTemporaryStore extends TemplateStore
{
	public function __construct(
		private TemporaryStore $store
	) {
	}

	protected function get(string $name): mixed
	{
		return $this->store->peek($name);
	}
}
