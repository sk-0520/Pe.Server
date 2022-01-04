<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\StringUtility;
use PeServer\Core\HttpStatus;
use PeServer\Core\Mvc\IActionResult;

/**
 * アクションメソッドの結果操作。
 */
abstract class ActionResult implements IActionResult
{
	/**
	 * 応答ヘッダ。
	 *
	 * @var array<string,array<string>|array{value:string,status:HttpStatus}>
	 */
	protected array $headers = array();

	/**
	 * 生成。
	 *
	 * @param array<string,array<string>|array{value:string,status:HttpStatus}> $headers 応答ヘッダ。
	 */
	protected function __construct(array $headers)
	{
		$this->headers = $headers;
	}

	/**
	 * 応答ヘッダを出力。
	 *
	 * @return void
	 */
	protected function header(): void
	{
		/** @var array<string,string> */
		$joinHeaders = [];
		/** @var array<string,array{value:string,status:HttpStatus}> */
		$codeHeaders = [];

		foreach ($this->headers as $k => $v) {
			if (array_key_exists('value', $v)) {
				$codeHeaders[$k] = $v;
			} else {
				/** @var string[] $v */
				$joinHeaders[$k] = StringUtility::join($v, ', ');
			}
		}

		foreach ($joinHeaders as $k => $v) {
			header("$k: $v");
		}
		foreach ($codeHeaders as $k => $v) {
			/** @var array{value:string,status:HttpStatus} $v */
			header("$k: " . $v['value'], true, $v['status']->code());
		}
	}

	/**
	 * 応答本文を出力。
	 *
	 * @return void
	 */
	protected abstract function body(): void;

	/**
	 * 応答処理。
	 *
	 * @return void
	 */
	public final function output(): void
	{
		$this->header();
		$this->body();
	}
}
