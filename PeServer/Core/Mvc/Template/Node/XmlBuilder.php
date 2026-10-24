<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use Override;
use PeServer\Core\Encoding;
use PeServer\Core\Mvc\Template\Node\Attribute\AttributesBase;
use PeServer\Core\Text;
use PeServer\Core\Throws\NotImplementedException;

class XmlBuilder extends NodeBuilder
{
	#region variable

	public string $version = "1.0";
	public Encoding $encoding;
	public bool|null $standalone = null;

	#endregion

	public function __construct()
	{
		$this->encoding = Encoding::getUtf8();
	}

	#region NodeBuilder

	#[Override]
	public function build(INode $node, ?BuildOptions $buildOptions = null): string
	{
		$xmlDeclaration = '<?xml version="' . $this->version . '" encoding="' . $this->encoding->name . '"';
		if ($this->standalone !== null) {
			$xmlDeclaration .= ' standalone="' . ($this->standalone ? 'yes' : 'no') . '"';
		}
		$xmlDeclaration .= '?>' . PHP_EOL;
		return $xmlDeclaration . parent::build($node, $buildOptions);
	}


	#endregion
}
