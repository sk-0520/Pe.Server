<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

interface IDatabaseImplementation
{
	#region function

	function escapeLike(string $value): string;

	function escapeValue(mixed $value): string;

	#endregion
}
