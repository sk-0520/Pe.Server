<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Mvc\ActionRequest;
use PeServer\Core\Store\CookieStore;
use PeServer\Core\Store\SessionStore;

/**
 * フィルタリング時の入力パラメータ。
 */
class FilterArgument
{
	/**
	 * リクエストパスの / 区切り分割配列。
	 *
	 * クエリは含まない。
	 *
	 * @var string[]
	 */
	public array $requestPaths;
	/**
	 * リクエストパスの / 結合文字列。
	 *
	 * クエリは含まない。
	 *
	 * @var string
	 */
	public string $requestPath;

	public CookieStore $cookie;
	public SessionStore $session;
	public ActionRequest $request;
	public ILogger $logger;

	/**
	 * Undocumented function
	 *
	 * @param string[] $requestPaths
	 * @param CookieStore $cookie
	 * @param SessionStore $session
	 * @param ActionRequest $request
	 * @param ILogger $logger
	 */
	public function __construct(array $requestPaths, CookieStore $cookie, SessionStore $session, ActionRequest $request, ILogger $logger)
	{
		$this->requestPaths = $requestPaths;
		$this->requestPath = StringUtility::join('/', $requestPaths);
		$this->cookie = $cookie;
		$this->session = $session;
		$this->request = $request;
		$this->logger = $logger;
	}
}
