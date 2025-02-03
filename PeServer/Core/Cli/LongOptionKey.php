<?php

declare(strict_types=1);

namespace PeServer\Core\Cli;

class LongOptionKey
{
	/**
	 *
	 * @param non-empty-string $key
	 * @param ParameterKind $kind
	 */
	public function __construct(
		public string $key,
		public ParameterKind $kind
	) {
		//NOP
	}
}
