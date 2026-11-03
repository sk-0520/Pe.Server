<?php

declare(strict_types=1);

namespace PeServerIT\App\Controllers\Page;

use PeServer\App\Models\AppCryptography;
use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Dao\Entities\SignUpWaitEmailsEntityDao;
use PeServer\App\Models\Dao\Entities\UserAuthenticationsEntityDao;
use PeServer\App\Models\SessionKey;
use PeServer\Core\Cryptography;
use PeServer\Core\DI\IDiRegisterContainer;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mail\EmailMessage;
use PeServer\Core\Mail\Mailer;
use PeServer\Core\Store\SessionStore;
use PeServer\Core\Store\TemporaryStore;
use PeServer\Core\Throws\HttpStatusException;
use PeServer\Core\Web\WebSecurity;
use PeServerTest\ItBody;
use PeServerTest\ItControllerClass;
use PeServerTest\ItMockStores;
use PeServerTest\ItOptions;
use PeServerTest\ItSetup;
use PeServerTest\ItUseDatabaseTrait;
use PHPUnit\Framework\Attributes\DataProvider;

class AccountSignupControllerTest extends ItControllerClass
{
	use ItUseDatabaseTrait;

	public static function provider_paths(): array
	{
		return [['/account/signup']];
	}

	#[DataProvider('provider_paths')]
	public function test_step1_get(string $path): void
	{
		$actual = $this->call(HttpMethod::Get, $path);
		$this->assertStatusOK($actual);
		$this->assertTitle('ユーザー登録 1/2', $actual);
		$this->assertCount(1, $actual->html->path()->collections("//form[@action='/account/signup'][@method='post']"));
		$this->assertCount(1, $actual->html->path()->collections("//input[@type='email'][@name='account_signup_email'][@required]"));
		$this->assertCount(1, $actual->html->path()->collections("//img[@class='token'][@width='56'][@height='28']"));
		$this->assertCount(2, $actual->html->path()->collections("//section[@class='email-information']//code[@data-clipboard='inline']"));
		$csrfName = $actual->container->get(WebSecurity::class)->getCsrfKind(WebSecurity::CSRF_KIND_REQUEST_NAME);
		$this->assertCount(1, $actual->html->path()->collections("//form//input[@name='$csrfName'][@type='hidden']"));
	}

	#[DataProvider('provider_paths')]
	public function test_step1_invalid(string $path): void
	{
		$options = new ItOptions(
			stores: ItMockStores::anonymous(signup1: true),
			body: ItBody::form([
				'account_signup_email' => 'invalid<&"',
				'account_signup_token' => 'invalid-token',
				'account_signup_value' => 'invalid-value',
			])
		);
		$actual = $this->call(HttpMethod::Post, $path, $options);
		$this->assertStatusOK($actual);
		$this->assertTitle('ユーザー登録 1/2', $actual);
		$this->assertVisibleCommonError([], $actual);
		$this->assertCount(2, $actual->html->path()->collections("//form[@action='/account/signup']//ul[contains(@class,'value-error')]"));
		$this->assertValue('invalid<&"', $actual->html->path()->collections("//input[@name='account_signup_email']")->single());
		$this->assertValue('', $actual->html->path()->collections("//input[@name='account_signup_value']")->single());
		$temporary = $actual->container->get(TemporaryStore::class)->peek('sign_up_token');
		$this->assertValue($temporary['token'], $actual->html->path()->collections("//input[@name='account_signup_token']")->single());
		$this->assertCount(1, $actual->html->path()->collections("//img[@data-hash='" . hash('sha256', $temporary['value']) . "']"));
	}

	#[DataProvider('provider_paths')]
	public function test_step1_send_and_notify(string $path): void
	{
		$mailer = $this->createMock(Mailer::class);
		$message = null;
		$mailer->expects($this->once())->method('send');
		$mailer->expects($this->once())->method('setMessage')->willReturnCallback(function (EmailMessage $value) use (&$message): void {
			$message = $value;
		});
		$actual = $this->call(HttpMethod::Post, $path, new ItOptions(
			stores: ItMockStores::anonymous(signup1: true),
			body: ItBody::form([
				'account_signup_email' => 'signup@example.com',
				'account_signup_token' => 'test-token',
				'account_signup_value' => '0123',
			])
		), function (ItSetup $setup) use ($mailer): void {
			$this->assertInstanceOf(IDiRegisterContainer::class, $setup->container);
			$setup->container->registerValue($mailer, Mailer::class);
			$setup->container->get(TemporaryStore::class)->push('sign_up_token', ['token' => 'test-token', 'value' => '0123']);
		});
		$this->assertRedirectPath(HttpStatus::Found, 'account/signup/notify', null, $actual);
		$pending = $actual->openDB()->querySingle('select token, email from sign_up_wait_emails');
		$token = $pending->fields['token'];
		$this->assertSame(80, strlen($token));
		$this->assertSame('signup@example.com', $actual->container->get(AppCryptography::class)->decrypt($pending->fields['email']));
		$this->assertInstanceOf(EmailMessage::class, $message);
		$this->assertStringContainsString('/account/signup/' . $token, $message->getHtml());
		$this->assertSame('signup@example.com', $mailer->toAddresses[0]->address);

		$notify = $this->call(HttpMethod::Get, $path . '/notify', new ItOptions(stores: ItMockStores::anonymous(signup1: true)));
		$this->assertStatusOK($notify);
		$this->assertTitle('ユーザー登録 メール通知', $notify);
		$this->assertTextNode('アカウント登録用URLを入力されたメールアドレスに送信しました。', $notify->html->path()->collections("//*[@id='content']/p/strong")->single());
		$this->assertCount(1, $notify->html->path()->collections("//section[@class='email-information']"));
	}

	#[DataProvider('provider_paths')]
	public function test_step2_get(string $path): void
	{
		$token = str_repeat('a', 80);
		$actual = $this->call(HttpMethod::Get, $path . '/' . $token);
		$this->assertStatusOK($actual);
		$this->assertTitle('ユーザー登録 2/2', $actual);
		$this->assertCount(1, $actual->html->path()->collections("//form[@action='/account/signup/$token'][@method='post']"));
		$this->assertCount(5, $actual->html->path()->collections("//form//input[@required]"));
		$this->assertCount(2, $actual->html->path()->collections("//form//input[@type='password'][@value='']"));
	}

	#[DataProvider('provider_paths')]
	public function test_step2_invalid(string $path): void
	{
		$token = str_repeat('b', 80);
		$actual = $this->call(HttpMethod::Post, $path . '/' . $token, new ItOptions(
			stores: ItMockStores::anonymous(signup2: true),
			body: ItBody::form([
				'account_signup_email' => 'invalid<&"',
				'account_signup_login_id' => '<bad>',
				'account_signup_password' => 'short',
				'account_signup_password_confirm' => 'different',
				'account_signup_name' => '',
			])
		));
		$this->assertStatusOK($actual);
		$this->assertTitle('ユーザー登録 2/2', $actual);
		$this->assertVisibleCommonError([], $actual);
		$this->assertCount(5, $actual->html->path()->collections("//form[@action='/account/signup/$token']//ul[contains(@class, 'value-error')]"));
		$this->assertCount(2, $actual->html->path()->collections("//input[@type='password'][@value=''][contains(@class,'error')]"));
		$this->assertValue('<bad>', $actual->html->path()->collections("//input[@name='account_signup_login_id']")->single());
		$this->assertValue('invalid<&"', $actual->html->path()->collections("//input[@name='account_signup_email']")->single());
	}

	#[DataProvider('provider_paths')]
	public function test_step2_email_mismatch(string $path): void
	{
		$token = str_repeat('d', 80);
		$actual = $this->call(HttpMethod::Post, $path . '/' . $token, new ItOptions(
			stores: ItMockStores::anonymous(signup2: true),
			body: ItBody::form([
				'account_signup_email' => 'different@example.com',
				'account_signup_login_id' => 'signup-user',
				'account_signup_password' => 'password123',
				'account_signup_password_confirm' => 'password123',
				'account_signup_name' => 'Signup User',
			])
		), function (ItSetup $setup) use ($token): void {
			$crypto = $setup->container->get(AppCryptography::class);
			(new SignUpWaitEmailsEntityDao($setup->databaseContext))->insertEmail(
				$token,
                $crypto->encrypt('signup@example.com'),
                $crypto->toMark('signup@example.com'),
                '127.0.0.1',
                'test'
			);
		});
		$this->assertStatusOK($actual);
		$this->assertVisibleCommonError([], $actual);
		$this->assertCount(1, $actual->html->path()->collections(
			"//form[@action='/account/signup/$token']//input[@name='account_signup_email'][contains(@class,'error')]/following-sibling::ul[contains(@class,'value-error')]/li"
		));
		$this->assertCount(2, $actual->html->path()->collections("//input[@type='password'][@value='']"));
		$this->assertSame(1, $actual->openDB()->selectSingleCount('select count(*) from sign_up_wait_emails'));
		$this->assertSame(0, $actual->openDB()->selectSingleCount("select count(*) from users where login_id = 'signup-user'"));
	}

	#[DataProvider('provider_paths')]
	public function test_step2_register(string $path): void
	{
		$token = str_repeat('c', 80);
		$mailer = $this->createMock(Mailer::class);
		$mailer->expects($this->once())->method('send');
		$mailer->expects($this->once())->method('setMessage')->with($this->callback(
			fn(EmailMessage $message) => str_contains($message->getHtml(), 'signup-user')
		));
		$actual = $this->call(HttpMethod::Post, $path . '/' . $token, new ItOptions(
			stores: ItMockStores::anonymous(signup2: true),
			body: ItBody::form([
				'account_signup_email' => 'signup@example.com',
				'account_signup_login_id' => 'signup-user',
				'account_signup_password' => 'password123',
				'account_signup_password_confirm' => 'password123',
				'account_signup_name' => 'Signup User',
			])
		), function (ItSetup $setup) use ($token, $mailer): void {
			$this->assertInstanceOf(IDiRegisterContainer::class, $setup->container);
			$setup->container->registerValue($mailer, Mailer::class);
			$crypto = $setup->container->get(AppCryptography::class);
			(new SignUpWaitEmailsEntityDao($setup->databaseContext))->insertEmail(
				$token,
                $crypto->encrypt('signup@example.com'),
                $crypto->toMark('signup@example.com'),
                '127.0.0.1',
                'test'
			);
		});
		$this->assertRedirectPath(HttpStatus::Found, '', null, $actual);
		$database = $actual->openDB();
		$user = $database->querySingle("select user_id, name from users where login_id = 'signup-user'");
		$this->assertSame('Signup User', $user->fields['name']);
		$password = (new UserAuthenticationsEntityDao($database))->selectPassword($user->fields['user_id']);
		$this->assertTrue(Cryptography::verifyPassword('password123', $password->fields['current_password']));
		$this->assertSame(0, $database->selectSingleCount('select count(*) from sign_up_wait_emails'));
		$account = $actual->container->get(SessionStore::class)->getOr(SessionKey::ACCOUNT, null);
		$this->assertNotNull($account);
		$this->assertSame($user->fields['user_id'], $account->userId);
		$this->assertSame(AuditLog::USER_CREATE, $this->getMaybeLatestAuditLog($database)->fields['event']);
		$this->assertSame('signup@example.com', $mailer->toAddresses[0]->address);
	}

	#[DataProvider('provider_paths')]
	public function test_notify_without_session(string $path): void
	{
		$this->expectException(HttpStatusException::class);
		$this->call(HttpMethod::Get, $path . '/notify');
	}
}
