<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\Http\HttpStatus;

/**
 * View側のテンプレート生成用パラメータ。
 */
class TemplateParameter
{
	/**
	 * 生成。
	 *
	 * @param HttpStatus $httpStatus ステータスコード。
	 * @param array<string,mixed> $values 値。
	 * @param array<string,string[]> $errors エラー一覧。
	 */
	public function __construct(
		public HttpStatus $httpStatus,
		public array $values,
		public array $errors
	) {
	}
}
