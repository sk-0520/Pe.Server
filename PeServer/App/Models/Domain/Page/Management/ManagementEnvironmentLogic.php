<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Management;

use DOMXPath;
use DOMElement;
use DOMDocument;
use PeServer\Core\Regex;
use PeServer\Core\OutputBuffer;
use PeServer\Core\Html\HtmlTagElement;
use PeServer\Core\Html\HtmlDocument;
use PeServer\Core\Mvc\Logic\LogicCallMode;
use PeServer\Core\Mvc\Logic\LogicParameter;
use PeServer\App\Models\Domain\Page\PageLogicBase;

class ManagementEnvironmentLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NOP
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		// phpinfo() 内容を無理やり出力するのです

		$rawPhpinfo = OutputBuffer::get(function () {
			phpinfo();
		});

		$phpDoc = new HtmlDocument($rawPhpinfo->raw);

		$xpath = $phpDoc->path();
		/** @var HtmlTagElement */
		$srcStyle = $xpath->query('//html/head/style')[0];
		/** @var HtmlTagElement */
		$srcContent = $xpath->query('//html/body/div')[0];

		$dom = new HtmlDocument();
		$content = $dom->addTagElement('div');
		$content->addClass('phpinfo');

		$dstStyle = $dom->importNode($srcStyle);
		$dstContent = $dom->importNode($srcContent);

		$css = $dstStyle->raw->textContent;
		$regex = new Regex();
		$newCss = $regex->replace(
			$css,
			'/^(.*)$/m',
			'.phpinfo $1'
		);
		$dstStyle->raw->textContent = $newCss;

		$content->appendChild($dstStyle);
		$content->appendChild($dstContent);

		$this->setValue('phpinfo', $dom->build());
	}
}
