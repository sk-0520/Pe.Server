<?php

declare(strict_types=1);

function before_update(array $scriptData)
{
	echo 'before_update';
}


function after_update(array $scriptData)
{
	echo 'after_update';
}
