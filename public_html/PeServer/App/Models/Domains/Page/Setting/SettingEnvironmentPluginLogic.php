<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Page\Setting;

use DOMXPath;
use DOMDocument;
use DOMElement;
use PeServer\Core\FileUtility;
use PeServer\Core\HtmlDocument;
use PeServer\Core\OutputBuffer;
use PeServer\Core\StringUtility;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\AppConfiguration;
use PeServer\Core\Throws\FileNotFoundException;
use PeServer\App\Models\Domains\Page\PageLogicBase;
use PeServer\Core\HtmlElement;

class SettingEnvironmentPluginLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NONE
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		// phpinfo() 内容を無理やり出力するのです

		$rawPhpinfo = OutputBuffer::get(function () {
			phpinfo();
		});

		$phpDom = new DOMDocument();
		libxml_use_internal_errors(true);
		$phpDom->loadHTML($rawPhpinfo->getRaw());

		$xpath = new DOMXPath($phpDom);
		$srcStyle = $xpath->query('//html/head/style')[0]; // @phpstan-ignore-line TODO: 後で対応する わかってる つらい
		$srcContent = $xpath->query('//html/body/div')[0]; // @phpstan-ignore-line TODO: 後で対応する

		//$srcStyle->content

		$dom = new HtmlDocument();
		$content = $dom->addElement('div');
		$content->addClass('phpinfo');

		$dstStyle = $dom->importNode($srcStyle);
		$dstContent = $dom->importNode($srcContent);

		/** @var DOMElement $dstStyle */
		$css = $dstStyle->textContent;
		$newCss = preg_replace(
			'/^(.*)$/m',
			'.phpinfo $1',
			$css
		);
		$dstStyle->textContent = $newCss; // @phpstan-ignore-line TODO: Regex に持たせる

		$content->appendChild($dstStyle);
		$content->appendChild($dstContent);

		$this->setValue('phpinfo', $dom->build());
	}
}