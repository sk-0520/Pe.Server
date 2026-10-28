<?php

declare(strict_types=1);

use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\Template\TemplateParameter;

class NodeTemplateParameter extends TemplateParameter
{
	public function __construct(
		HttpStatus $httpStatus
	) {
		parent::__construct($httpStatus, [], []);
	}
}

	//  * @param HttpStatus $httpStatus ステータスコード。
	//  * @param array<string,mixed> $values 値。
	//  * @param array<string,string[]> $errors エラー一覧。
