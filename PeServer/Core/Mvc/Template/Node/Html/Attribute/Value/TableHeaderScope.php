<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

enum TableHeaderScope: string
{
	case Row = "row";
	case Col = "col";
	case RowGroup = "rowgroup";
	case ColGroup = "colgroup";
}
