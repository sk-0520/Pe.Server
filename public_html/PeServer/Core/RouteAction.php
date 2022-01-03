<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\HttpStatus;
use PeServer\Core\IActionFilter;


class RouteAction
{
	public HttpStatus $status;
	public string $className;
	public string $classMethod;
	/**
	 * パラメータ。
	 *
	 * @var array<string,string>
	 */
	public array $params;
	/**
	 * フィルタ一覧。
	 *
	 * @var IActionFilter[]
	 */
	public array $filters;

	/**
	 * 生成。
	 *
	 * @param HttpStatus $status
	 * @param string $className
	 * @param string $classMethod
	 * @param array<string,string> $params
	 * @param IActionFilter[] $filters
	 */
	public function __construct(HttpStatus $status, string $className, string $classMethod, array $params, array $filters)
	{
		$this->status = $status;
		$this->className = $className;
		$this->classMethod = $classMethod;
		$this->params = $params;
		$this->filters = $filters;
	}
}
