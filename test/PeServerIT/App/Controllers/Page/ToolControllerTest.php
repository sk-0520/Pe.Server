<?php

declare(strict_types=1);

namespace PeServerIT\App\Controllers\Page;

use PeServer\Core\Http\HttpMethod;
use PeServerTest\ItActual;
use PeServerTest\ItBody;
use PeServerTest\ItControllerClass;
use PeServerTest\ItOptions;

class ToolControllerTest extends ItControllerClass
{
	public function test_index(): void
	{
		$actual = $this->call(HttpMethod::Get, '/tool');
		$this->assertStatusOK($actual);
		$this->assertTitle('ツール', $actual);
		$this->assertNodeTemplate($actual);
		$this->assertCount(1, $actual->html->path()->collections("//*[@id='content']//a[@href='/tool/base64']"));
		$this->assertCount(1, $actual->html->path()->collections("//*[@id='content']//a[@href='/tool/json']"));
	}

	public function test_base64_get(): void
	{
		$actual = $this->call(HttpMethod::Get, '/tool/base64');
		$this->assertStatusOK($actual);
		$this->assertTitle('ツール/base64', $actual);
		$this->assertNodeTemplate($actual);
		$this->assertCount(1, $actual->html->path()->collections("//form[@action='/tool/base64'][@method='post']"));
		$this->assertCount(1, $actual->html->path()->collections("//textarea[@name='tool_base64_input'][@required]"));
		$this->assertCount(1, $actual->html->path()->collections("//input[@type='radio'][@name='tool_base64_kind'][@value='encode'][@checked]"));
	}

	public function test_base64_post(): void
	{
		$actual = $this->call(HttpMethod::Post, '/tool/base64', new ItOptions(
			body: ItBody::form([
				'tool_base64_input' => 'Pe',
				'tool_base64_kind' => 'encode',
			])
		));
		$this->assertStatusOK($actual);
		$this->assertNodeTemplate($actual);
		$this->assertValue('Pe', $actual->html->path()->collections("//textarea[@name='tool_base64_input']")->single());
		$this->assertCount(1, $actual->html->path()->collections("//pre[@data-clipboard='block'][text()='UGU=']"));
	}

	public function test_base64_invalid_post(): void
	{
		$actual = $this->call(HttpMethod::Post, '/tool/base64', new ItOptions(
			body: ItBody::form([
				'tool_base64_input' => '',
				'tool_base64_kind' => 'encode',
			])
		));
		$this->assertStatusOK($actual);
		$this->assertCount(1, $actual->html->path()->collections("//form[@action='/tool/base64']"));
		$this->assertCount(1, $actual->html->path()->collections("//ul[contains(@class,'value-error')]"));
	}

	public function test_json_post(): void
	{
		$actual = $this->call(HttpMethod::Post, '/tool/json', new ItOptions(
			body: ItBody::form([
				'tool_json_input' => '{"ok":true}',
				'tool_json_kind' => 'format',
			])
		));
		$this->assertStatusOK($actual);
		$this->assertTitle('ツール/Json', $actual);
		$this->assertNodeTemplate($actual);
		$this->assertCount(1, $actual->html->path()->collections("//input[@type='radio'][@name='tool_json_kind'][@value='format'][@checked]"));
		$this->assertStringContainsString('"ok": true', $actual->html->path()->collections("//pre[@data-clipboard='block']")->single()->raw->textContent);
	}

	public function test_json_get(): void
	{
		$actual = $this->call(HttpMethod::Get, '/tool/json');
		$this->assertStatusOK($actual);
		$this->assertTitle('ツール/Json', $actual);
		$this->assertNodeTemplate($actual);
		$this->assertCount(1, $actual->html->path()->collections("//form[@action='/tool/json'][@method='post']"));
		$this->assertCount(1, $actual->html->path()->collections("//textarea[@name='tool_json_input'][@required]"));
	}

	private function assertNodeTemplate(ItActual $actual): void
	{
		$indicators = $actual->html->path()->collections("//*[@id='node-template-indicator']");
		$this->assertCount(1, $indicators);
		$this->assertTextNode('2', $indicators->single());
	}

}
