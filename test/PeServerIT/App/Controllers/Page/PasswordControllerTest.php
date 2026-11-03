<?php

declare(strict_types=1);

namespace PeServerIT\App\Controllers\Page;

use PeServer\App\Models\AppCryptography;
use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Dao\Entities\UserAuthenticationsEntityDao;
use PeServer\App\Models\Dao\Entities\UsersEntityDao;
use PeServer\App\Models\Data\SessionAnonymous;
use PeServer\App\Models\Domain\UserLevel;
use PeServer\App\Models\Domain\UserState;
use PeServer\App\Models\SessionKey;
use PeServer\Core\Cryptography;
use PeServer\Core\DI\IDiRegisterContainer;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mail\EmailMessage;
use PeServer\Core\Mail\Mailer;
use PeServer\Core\Store\SessionStore;
use PeServer\Core\Throws\HttpStatusException;
use PeServer\Core\Web\WebSecurity;
use PeServerTest\ItBody;
use PeServerTest\ItActual;
use PeServerTest\ItControllerClass;
use PeServerTest\ItMockStores;
use PeServerTest\ItOptions;
use PeServerTest\ItSetup;
use PeServerTest\ItUseDatabaseTrait;
use PHPUnit\Framework\Attributes\DataProvider;

class PasswordControllerTest extends ItControllerClass
{
	use ItUseDatabaseTrait;

	private const TOKEN = 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';

	public static function provider_routes(): array
	{
		return [['/password', '']];
	}

	private function seed(ItSetup $setup): void
	{
		$crypto = $setup->container->get(AppCryptography::class);
		(new UsersEntityDao($setup->databaseContext))->insertUser(
			ItMockStores::SESSION_ACCOUNT_USER_ID,
            'reset-user',
            UserLevel::USER,
            UserState::ENABLED,
            'Reset User',
			$crypto->encrypt('reset@example.com'),
            $crypto->toMark('reset@example.com'),
            '',
            '',
            ''
		);
		$dao = new UserAuthenticationsEntityDao($setup->databaseContext);
		$dao->insertUserAuthentication(ItMockStores::SESSION_ACCOUNT_USER_ID, Cryptography::hashPassword('old-password123'));
		$dao->updatePasswordReminder(ItMockStores::SESSION_ACCOUNT_USER_ID, self::TOKEN);
	}

	private function assertForm(string $path, ItActual $actual): void
	{
		$this->assertCount(1, $actual->html->path()->collections("//form[@action='$path'][@method='post']"));
		$name = $actual->container->get(WebSecurity::class)->getCsrfKind(WebSecurity::CSRF_KIND_REQUEST_NAME);
		$this->assertCount(1, $actual->html->path()->collections("//form//input[@name='$name'][@type='hidden']"));
		$this->assertCount(1, $actual->html->path()->collections("//section[@class='email-information']"));
		$this->assertCount(0, $actual->html->path()->collections("//*[@id='node-template-indicator']"));
	}

	#[DataProvider('provider_routes')]
	public function test_reminder_get(string $base, string $suffix): void
	{
		$path = "$base/reminder$suffix";
		$actual = $this->call(HttpMethod::Get, $path);
		$this->assertStatusOK($actual);
		$this->assertTitle('パスワード再設定', $actual);
		$this->assertForm($path, $actual);
		$this->assertCount(1, $actual->html->path()->collections("//input[@name='reminder_login_id'][@type='text'][@required]"));
		$anonymous = $actual->container->get(SessionStore::class)->getOr(SessionKey::ANONYMOUS, null);
		$this->assertInstanceOf(SessionAnonymous::class, $anonymous);
		$this->assertTrue($anonymous->passwordReminder);
	}

	#[DataProvider('provider_routes')]
	public function test_reminder_empty(string $base, string $suffix): void
	{
		$path = "$base/reminder$suffix";
		$actual = $this->call(HttpMethod::Post, $path, new ItOptions(
			stores: ItMockStores::anonymous(passwordReminder: true),
			body: ItBody::form(['reminder_login_id' => ''])
		));
		$this->assertStatusOK($actual);
		$this->assertForm($path, $actual);
		$this->assertVisibleCommonError([], $actual);
		$this->assertCount(1, $actual->html->path()->collections("//input[@name='reminder_login_id'][contains(@class,'error')]/following-sibling::ul/li"));
	}

	#[DataProvider('provider_routes')]
	public function test_reminder_send_and_notify(string $base, string $suffix): void
	{
		$mailer = $this->createMock(Mailer::class);
		$message = null;
		$mailer->expects($this->once())->method('send');
		$mailer->expects($this->once())->method('setMessage')->willReturnCallback(function (EmailMessage $value) use (&$message): void {
			$message = $value;
		});
		$sent = $this->call(HttpMethod::Post, "$base/reminder$suffix", new ItOptions(
			stores: ItMockStores::anonymous(passwordReminder: true),
			body: ItBody::form(['reminder_login_id' => 'reset-user'])
		), function (ItSetup $setup) use ($mailer): void {
			$this->seed($setup);
			$this->assertInstanceOf(IDiRegisterContainer::class, $setup->container);
			$setup->container->registerValue($mailer, Mailer::class);
		});
		$row = $sent->openDB()->querySingle('select reminder_token, reminder_timestamp from user_authentications where user_id = :id', [
			'id' => ItMockStores::SESSION_ACCOUNT_USER_ID,
		])->fields;
		$token = $row['reminder_token'];
		$this->assertMatchesRegularExpression('/^[a-zA-Z0-9]{80}$/', $token);
		$this->assertNotSame(self::TOKEN, $token);
		$this->assertNotEmpty($row['reminder_timestamp']);
		$this->assertRedirectPath(HttpStatus::Found, "password/reminding/$token", null, $sent);
		$this->assertInstanceOf(EmailMessage::class, $message);
		$this->assertStringContainsString("/password/reset/$token", $message->getHtml());
		$this->assertSame('reset@example.com', $mailer->toAddresses[0]->address);
		$this->assertSame(AuditLog::USER_PASSWORD_REMINDER_TOKEN, $this->getMaybeLatestAuditLog($sent->openDB())->fields['event']);

		$notify = $this->call(HttpMethod::Get, "$base/reminding$suffix/$token", new ItOptions(
			stores: ItMockStores::anonymous(passwordReminder: true)
		), null, $sent);
		$this->assertStatusOK($notify);
		$this->assertTitle('パスワード再発行メール送信', $notify);
		$this->assertTextNode('パスワード再発行メールを送信しました。', $notify->html->path()->collections("//*[@id='content']/p[1]")->single());
		$this->assertCount(1, $notify->html->path()->collections("//section[@class='email-information']"));

		$resetPath = "$base/reset$suffix/$token";
		$reset = $this->call(HttpMethod::Get, $resetPath, previousActual: $notify);
		$this->assertStatusOK($reset);
		$this->assertForm($resetPath, $reset);
		$updated = $this->call(HttpMethod::Post, $resetPath, new ItOptions(
			stores: ItMockStores::anonymous(passwordReset: true),
			body: ItBody::form([
				'reminder_login_id' => 'reset-user',
				'reminder_password_new' => 'mail-link-password123',
				'reminder_password_confirm' => 'mail-link-password123',
			])
		), null, $reset);
		$this->assertRedirectPath(HttpStatus::Found, '', null, $updated);
		$dao = new UserAuthenticationsEntityDao($updated->openDB());
		$this->assertTrue(Cryptography::verifyPassword(
			'mail-link-password123',
            $dao->selectPassword(ItMockStores::SESSION_ACCOUNT_USER_ID)->fields['current_password']
		));
		$this->assertFalse($dao->selectExistsToken($token, 60));
	}

	#[DataProvider('provider_routes')]
	public function test_reminder_unknown_login(string $base, string $suffix): void
	{
		$mailer = $this->createMock(Mailer::class);
		$mailer->expects($this->once())->method('send');
		$mailer->expects($this->once())->method('setMessage')->with($this->callback(
			fn(EmailMessage $message) => $message->hasText() && !$message->hasHtml()
		));
		$actual = $this->call(HttpMethod::Post, "$base/reminder$suffix", new ItOptions(
			stores: ItMockStores::anonymous(passwordReminder: true),
			body: ItBody::form(['reminder_login_id' => 'not-registered'])
		), function (ItSetup $setup) use ($mailer): void {
			$this->assertInstanceOf(IDiRegisterContainer::class, $setup->container);
			$setup->container->registerValue($mailer, Mailer::class);
		});
		$this->assertSame(HttpStatus::Found, $actual->getHttpStatus());
		$this->assertStringStartsWith('<SPAM-REMINDER>', $mailer->subject);
	}

	#[DataProvider('provider_routes')]
	public function test_reset_get(string $base, string $suffix): void
	{
		$path = "$base/reset$suffix/" . self::TOKEN;
		$actual = $this->call(HttpMethod::Get, $path);
		$this->assertStatusOK($actual);
		$this->assertTitle('パスワード再発行最終工程', $actual);
		$this->assertForm($path, $actual);
		$this->assertCount(3, $actual->html->path()->collections("//form//input[@required]"));
		$this->assertCount(2, $actual->html->path()->collections("//input[@type='password'][@value='']"));
		$anonymous = $actual->container->get(SessionStore::class)->getOr(SessionKey::ANONYMOUS, null);
		$this->assertInstanceOf(SessionAnonymous::class, $anonymous);
		$this->assertTrue($anonymous->passwordReset);
	}

	#[DataProvider('provider_routes')]
	public function test_reset_invalid(string $base, string $suffix): void
	{
		$path = "$base/reset$suffix/" . self::TOKEN;
		$actual = $this->call(HttpMethod::Post, $path, new ItOptions(
			stores: ItMockStores::anonymous(passwordReset: true),
			body: ItBody::form([
				'reminder_login_id' => 'login<&"',
				'reminder_password_new' => 'x',
				'reminder_password_confirm' => 'different',
			])
		));
		$this->assertStatusOK($actual);
		$this->assertForm($path, $actual);
		$this->assertVisibleCommonError([], $actual);
		$this->assertValue('login<&"', $actual->html->path()->collections("//input[@name='reminder_login_id']")->single());
		$this->assertCount(2, $actual->html->path()->collections("//input[@type='password'][@value=''][contains(@class,'error')]/following-sibling::ul"));
	}

	#[DataProvider('provider_routes')]
	public function test_reset_success(string $base, string $suffix): void
	{
		$actual = $this->call(HttpMethod::Post, "$base/reset$suffix/" . self::TOKEN, new ItOptions(
			stores: ItMockStores::anonymous(passwordReset: true),
			body: ItBody::form([
				'reminder_login_id' => 'reset-user',
				'reminder_password_new' => 'new-password123',
				'reminder_password_confirm' => 'new-password123',
			])
		), $this->seed(...));
		$this->assertRedirectPath(HttpStatus::Found, '', null, $actual);
		$row = $actual->openDB()->querySingle('select current_password, reminder_token, reminder_timestamp from user_authentications where user_id = :id', [
			'id' => ItMockStores::SESSION_ACCOUNT_USER_ID,
		])->fields;
		$this->assertTrue(Cryptography::verifyPassword('new-password123', $row['current_password']));
		$this->assertSame('', $row['reminder_token']);
		$this->assertNull($row['reminder_timestamp']);
		$this->assertSame(AuditLog::USER_PASSWORD_REMINDER_RESET, $this->getMaybeLatestAuditLog($actual->openDB())->fields['event']);
	}

	public static function provider_missing_session(): array
	{
		return [
			[HttpMethod::Post, '/password/reminder'],
			[HttpMethod::Get, '/password/reminding/' . self::TOKEN],
			[HttpMethod::Post, '/password/reset/' . self::TOKEN],
		];
	}

	public function test_root(): void
	{
		$actual = $this->call(HttpMethod::Get, '/password');
		$this->assertStatusOK($actual);
		$this->assertTitle('パスワード再設定', $actual);
		$this->assertForm('/password/reminder', $actual);
	}

	#[DataProvider('provider_missing_session')]
	public function test_missing_session(HttpMethod $method, string $path): void
	{
		$this->expectException(HttpStatusException::class);
		$this->call($method, $path);
	}
}
