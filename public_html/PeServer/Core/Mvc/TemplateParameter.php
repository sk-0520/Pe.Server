<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\HttpStatus;

/**
 * View側のテンプレート生成用パラメータ。
 */
class TemplateParameter
{
	/**
	 * ステータスコード。
	 *
	 * @var HttpStatus
	 */
	public HttpStatus $httpStatus;

	/**
	 * 値。
	 *
	 * @var array<string,string|string[]|bool|int>
	 */
	public array $values;

	/**
	 * エラー一覧。
	 *
	 * @var array<string,string[]>
	 */
	public array $errors;

	/**
	 * Undocumented function
	 *
	 * @param HttpStatus $httpStatus
	 * @param array<string,string|string[]|bool|int> $values
	 * @param array<string,string[]> $errors
	 */
	public function __construct(HttpStatus $httpStatus, array $values, array $errors)
	{
		$this->httpStatus = $httpStatus;
		$this->values = $values;
		$this->errors = $errors;
	}
}
