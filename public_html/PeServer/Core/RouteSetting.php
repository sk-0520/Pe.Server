<?php

declare(strict_types=1);

namespace PeServer\Core;

class RouteSetting
{
	/**
	 * Undocumented variable
	 *
	 * @var IActionFilter[]
	 */
	public array $globalFilters;
	/**
	 * Undocumented variable
	 *
	 * @var IActionFilter[]
	 */
	public array $actionFilters;
	/**
	 * Undocumented variable
	 *
	 * @var Route[]
	 */
	public array $routes;

	/**
	 * 生成。
	 *
	 * @param IActionFilter[] $globalFilters
	 * @param IActionFilter[] $actionFilters
	 * @param Route[] $routes
	 */
	public function __construct(array $globalFilters, array $actionFilters, array $routes)
	{
		$this->globalFilters = $globalFilters;
		$this->actionFilters = $actionFilters;
		$this->routes = $routes;
	}
}
