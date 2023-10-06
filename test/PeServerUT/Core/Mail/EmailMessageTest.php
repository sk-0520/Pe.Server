<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mail;

use PeServer\Core\Mail\EmailMessage;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\InvalidOperationException;
use PeServerTest\TestClass;

class EmailMessageTest extends TestClass
{
	public function test_text()
	{
		$msg1 = new EmailMessage();
		$this->assertFalse($msg1->hasText());
		try {
			$msg1->getText();
			$this->fail();
		} catch (InvalidOperationException) {
			$this->success();
		}

		$msg2 = new EmailMessage('');
		$this->assertFalse($msg2->hasText());
		try {
			$msg2->setText('');
			$this->fail();
		} catch (ArgumentException) {
			$this->success();
		}

		$msg3 = new EmailMessage(' ');
		$this->assertFalse($msg3->hasText());
		try {
			$msg3->setText(' ');
			$this->fail();
		} catch (ArgumentException) {
			$this->success();
		}

		$msg4 = new EmailMessage('a');
		$this->assertTrue($msg4->hasText());
		$this->assertSame('a', $msg4->getText());

		$msg4->setText('abc');
		$this->assertTrue($msg4->hasText());
		$this->assertSame('abc', $msg4->getText());

		$msg4->clearText();
		$this->assertFalse($msg4->hasText());
	}

	public function test_html()
	{
		$msg1 = new EmailMessage();
		$this->assertFalse($msg1->hasHtml());
		try {
			$msg1->getHtml();
			$this->fail();
		} catch (InvalidOperationException) {
			$this->success();
		}

		$msg2 = new EmailMessage(null, '');
		$this->assertFalse($msg2->hasHtml());
		try {
			$msg2->setHtml('');
			$this->fail();
		} catch (ArgumentException) {
			$this->success();
		}

		$msg3 = new EmailMessage(null, ' ');
		$this->assertFalse($msg3->hasHtml());
		try {
			$msg3->setHtml(' ');
			$this->fail();
		} catch (ArgumentException) {
			$this->success();
		}

		$msg4 = new EmailMessage(null, 'a');
		$this->assertTrue($msg4->hasHtml());
		$this->assertSame('a', $msg4->getHtml());

		$msg4->setHtml('abc');
		$this->assertTrue($msg4->hasHtml());
		$this->assertSame('abc', $msg4->getHtml());

		$msg4->clearHtml();
		$this->assertFalse($msg4->hasHtml());
	}
}
