<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node;

use PeServer\Core\Mvc\Template\Node\BuildOptions;
use PeServer\Core\Mvc\Template\Node\NodeBase;
use PeServer\Core\Mvc\Template\Node\NodeBuilder;
use PeServerTest\TestClass;
use Override;

class NodeBuilderTest extends TestClass
{
	public function test_build_passes_extended_build_options_to_node(): void
	{
		$node = new CustomOptionsCaptureNode();
		$buildOptions = new ExtendedBuildOptions('value');

		(new NodeBuilder())->build($node, $buildOptions);

		$this->assertSame($buildOptions, $node->received);
		$this->assertSame('value', $node->received?->customValue);
	}
}

readonly class ExtendedBuildOptions extends BuildOptions
{
	public function __construct(public string $customValue)
	{
		parent::__construct(BuildOptions::FORMAT_NONE, '');
	}
}

class CustomOptionsCaptureNode extends NodeBase
{
	public ?BuildOptions $received = null;

	#[Override]
	public function hasChildren(): bool
	{
		return false;
	}

	#[Override]
	public function build(int $level, BuildOptions $buildOptions): string
	{
		$this->received = $buildOptions;
		return '';
	}
}
