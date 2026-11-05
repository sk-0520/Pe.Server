<?php

declare(strict_types=1);

namespace PeServerIT\App\Controllers\Page;

use PeServer\App\Models\AppCryptography;
use PeServer\App\Models\Dao\Entities\CrashReportsEntityDao;
use PeServer\App\Models\Dao\Entities\FeedbacksEntityDao;
use PeServer\App\Models\Dao\Entities\PluginCategoriesEntityDao;
use PeServer\App\Models\Dao\Entities\UsersEntityDao;
use PeServer\App\Models\Data\ReportStatus;
use PeServer\App\Models\Domain\Page\Management\ManagementLogDetailLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementLogListLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementEnvironmentLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementMailSendLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementDatabaseDownloadLogic;
use PeServer\App\Models\Domain\Page\Management\ManagementDatabaseMaintenanceLogic;
use PeServer\App\Models\Domain\Page\ManagementControl\ManagementControlBackupDownloadLogic;
use PeServer\App\Models\Domain\Page\ManagementControl\ManagementControlBackupListLogic;
use PeServer\App\Models\Domain\UserLevel;
use PeServer\App\Models\Domain\UserState;
use PeServer\Core\Binary;
use PeServer\Core\Archive\Archiver;
use PeServer\Core\DI\DiItem;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mime;
use PeServer\Core\Mvc\Content\DownloadDataContent;
use PeServer\Core\Mvc\Template\TemplateParameter;
use PeServer\Core\Web\WebSecurity;
use PeServerTest\ItActual;
use PeServerTest\ItBody;
use PeServerTest\ItControllerClass;
use PeServerTest\ItMockStores;
use PeServerTest\ItOptions;
use PeServerTest\ItSetup;
use PeServerTest\ItUseDatabaseTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\BackupGlobals;

#[BackupGlobals(true)]
class ManagementControllerTest extends ItControllerClass
{
	use ItUseDatabaseTrait;

	private function options(array $body = [], string $level = UserLevel::ADMINISTRATOR): ItOptions
	{
		return new ItOptions(stores: ItMockStores::account($level), body: ItBody::form($body));
	}

	private function seed(ItSetup $setup): void
	{
		$_SERVER['HTTP_HOST'] = 'localhost';
		$_SERVER['SERVER_PORT'] = '80';
		$crypto = $setup->container->get(AppCryptography::class);
		(new UsersEntityDao($setup->databaseContext))->insertUser(
			ItMockStores::SESSION_ACCOUNT_USER_ID,
            ItMockStores::SESSION_ACCOUNT_LOGIN_ID,
			UserLevel::ADMINISTRATOR,
            UserState::ENABLED,
            'Admin <test>',
			$crypto->encrypt('admin@example.com'),
            $crypto->toMark('admin@example.com'),
            '',
            '',
            ''
		);
		(new PluginCategoriesEntityDao($setup->databaseContext))->insertPluginCategory('test-category', 'Category <test>', '"<&');
		$feedbacks = new FeedbacksEntityDao($setup->databaseContext);
		$crashes = new CrashReportsEntityDao($setup->databaseContext);
		for ($i = 0; $i < 26; $i++) {
			$feedbacks->insertFeedbacks(
                '127.0.0.1',
                '1.2.3',
                'revision',
                'build',
                'test-user',
                '2026-01-01',
                '1.0.0',
                'x64',
                'x64',
                'Windows',
                'CLR',
                'test',
                'Subject <test>',
                '**feedback**'
            );
			$crashes->insertCrashReports(
                '127.0.0.1',
                '1.2.3',
                'revision',
                'build',
                'test-user',
                'Exception <test>',
                '',
                '',
                new Binary('')
            );
		}
		// CLI の phpinfo は HTML にならない。ファイル表示も固定し開発環境を操作しない。
		foreach (
            [
			ManagementEnvironmentLogic::class => ['phpinfo' => '<div class="phpinfo"><table><tr><td>PHP fixture</td></tr></table></div>'],
			ManagementLogListLogic::class => ['directory' => 'test-logs', 'log_files' => [['name' => 'test.log', 'size' => 12, 'human_size' => '12 B']]],
			ManagementLogDetailLogic::class => ['log_name' => 'test.log', 'log_file' => 'test-logs\test.log', 'log_value' => 'Log <test>'],
			ManagementControlBackupListLogic::class => ['directory' => 'test-backups', 'items' => [['name' => 'test.zip', 'size' => 12, 'human_size' => '12 B']]],
            ] as $class => $values
		) {
			$logic = $this->createStub($class);
			$logic->method('run')->willReturn(true);
			$logic->method('getViewData')->willReturn(new TemplateParameter(HttpStatus::OK, $values, []));
			if ($class === ManagementLogDetailLogic::class) {
				$logic->method('equalsResult')->willReturn(false);
			}
			$setup->container->remove($class);
			$setup->container->add($class, DiItem::factory(fn() => $logic));
		}
	}

	private function assertNode(ItActual $actual): void
	{
		$this->assertCount(0, $actual->html->path()->collections("//*[@id='node-template-indicator']"));
	}

	private function assertCsrf(ItActual $actual): void
	{
		$name = $actual->container->get(WebSecurity::class)->getCsrfKind(WebSecurity::CSRF_KIND_REQUEST_NAME);
		$this->assertCount(1, $actual->html->path()->collections("//form//input[@type='hidden'][@name='$name']"));
	}

	public static function provider_get(): array
	{
		return [
			['/management', '管理', null, null],
			['/management/setup', 'セットアップ', '/management/setup', null],
			['/management/environment', '環境情報', null, null],
			['/management/configuration', '現在設定', null, null],
			['/management/configuration/edit', '現在設定', '/management/configuration/edit', null],
			['/management/database-maintenance/default', 'DBメンテナンス(default)', '/management/database-maintenance/default', 'management_database_maintenance'],
			['/management/mail-send', 'メール送信', '/management/mail-send', null],
			['/management/php-evaluate', 'PHP実行', '/management/php-evaluate', null],
			['/management/default-plugin', '標準プラグイン', '/management/default-plugin', null],
			['/management/plugin-category', 'プラグインカテゴリ', null, 'management_plugin_category'],
			['/management/version', 'バージョン設定', '/management/version', 'management_version'],
			['/management/feedback', 'フィードバック', null, 'management_feedback_list'],
			['/management/feedback/page/2', 'フィードバック', null, 'management_feedback_list'],
			['/management/feedback/1', 'フィードバック: 1', '/management/feedback/1', null],
			['/management/crash-report', 'クラッシュレポート', null, 'management_crash_report_list'],
			['/management/crash-report/page/2', 'クラッシュレポート', null, 'management_crash_report_list'],
			['/management/crash-report/1', 'クラッシュレポート: 1', '/management/crash-report/1', null],
			['/management/log', 'ログ一覧', null, 'management_log_list'],
			['/management/log/test.log', 'ログ詳細: test.log', null, null],
			['/management/markdown', 'Markdown', null, null],
			['/management/control/user', 'ユーザー一覧', null, null],
			['/management/control/backup', 'バックアップ一覧', null, null],
		];
	}

	#[DataProvider('provider_get')]
	public function test_get(string $path, string $title, ?string $action, ?string $script): void
	{
		$actual = $this->call(HttpMethod::Get, $path, $this->options(), $this->seed(...));
		$this->assertStatusOK($actual);
		$this->assertTitle($title, $actual);
		$this->assertNode($actual);
		$this->assertCount(0, $actual->html->path()->collections("//*[@id='content']//script"));
		if ($action !== null) {
			$this->assertCount(1, $actual->html->path()->collections("//form[@action='$action'][@method='post']"));
			if ($path !== '/management/setup') {
				$this->assertCsrf($actual);
			}
		}
		if ($script !== null) {
			$this->assertCount(1, $actual->html->path()->collections("//script[contains(@src,'/scripts/$script.js')]"));
		}
		if (in_array($path, ['/management/log', '/management/plugin-category', '/management/feedback', '/management/crash-report'], true)) {
			$id = $actual->container->get(WebSecurity::class)->getCsrfKind(WebSecurity::CSRF_KIND_REQUEST_ID);
			$this->assertCount(1, $actual->html->path()->collections("//head/meta[@id='$id'][@name='$id'][@content]"));
		}
		if (str_ends_with($path, '/page/2')) {
			$this->assertCount(1, $actual->html->path()->collections("//table/tbody/tr"));
		}
	}

	public static function provider_invalid_post(): array
	{
		return [
			['/management/setup', ['setting_setup_login_id' => '<bad>'], 'setting_setup_login_id'],
			['/management/configuration/edit', ['json' => '<invalid json>'], 'json'],
			['/management/database-maintenance/default', ['database_maintenance_statement' => ''], 'database_maintenance_statement'],
			['/management/mail-send', ['mail_to' => '<invalid email>', 'mail_subject' => 'Subject <test>', 'mail_body' => ''], 'mail_to'],
			['/management/php-evaluate', ['php_statement' => ''], 'php_statement'],
			['/management/version', ['version' => ''], 'version'],
		];
	}

	#[DataProvider('provider_invalid_post')]
	public function test_invalid_post(string $path, array $body, string $key): void
	{
		$actual = $this->call(HttpMethod::Post, $path, $this->options($body), $this->seed(...));
		$this->assertNode($actual);
		$this->assertCount(1, $actual->html->path()->collections("//form[@action='$path']"));
		$this->assertCount(1, $actual->html->path()->collections("//*[@name='$key'][contains(@class,'error')]/following-sibling::ul"));
		$this->assertCount(0, $actual->html->path()->collections("//invalid"));
	}

	public function test_database_query_and_toggle(): void
	{
		$actual = $this->call(HttpMethod::Post, '/management/database-maintenance/default', $this->options([
			'database_maintenance_statement' => "select '<test>' as sample",
			'tables_open' => 'true',
		]), $this->seed(...));
		$this->assertNode($actual);
		$this->assertTextNode('<test>', $actual->html->path()->collections("//table/tbody/tr/td")->single());
		$this->assertCount(1, $actual->html->path()->collections("//details[@id='tables'][@open]"));
		$this->assertCount(1, $actual->html->path()->collections("//input[@id='tables_open'][@value='true']"));
		$this->assertGreaterThan(0, $actual->html->path()->collections("//button[@class='pg-table'][@type='button'][@data-table][@data-columns]")->count());
		$this->assertCount(1, $actual->html->path()->collections("//script[contains(text(),'tablesElement.open')]"));
	}

	public function test_php_execution(): void
	{
		$actual = $this->call(HttpMethod::Post, '/management/php-evaluate', $this->options([
			'php_statement' => 'echo "<output>"; return ["value" => "<result>"];',
		]), $this->seed(...));
		$this->assertNode($actual);
		$this->assertCount(1, $actual->html->path()->collections("//pre[text()='<output>']"));
		$this->assertCount(1, $actual->html->path()->collections("//dt[text()='結果']/following-sibling::dd[1]/pre[contains(text(),'<result>')]"));
		$this->assertCount(0, $actual->html->path()->collections("//output|//result"));
	}

	public static function provider_report(): array
	{
		return [['feedback'], ['crash-report']];
	}

	#[DataProvider('provider_report')]
	public function test_report_save(string $kind): void
	{
		$path = "/management/$kind/1";
		$actual = $this->call(HttpMethod::Post, $path, $this->options([
			'developer-status' => ReportStatus::Completed->value,
			'developer-comment' => 'Memo <test>',
		]), $this->seed(...));
		$this->assertRedirectPath(HttpStatus::Found, "management/$kind/1", null, $actual);
		$next = $this->call(HttpMethod::Get, $path, $this->options(), null, $actual);
		$this->assertNode($next);
		$this->assertCount(1, $next->html->path()->collections("//input[@name='developer-status'][@value='" . ReportStatus::Completed->value . "'][@checked]"));
		$this->assertTextNode('Memo <test>', $next->html->path()->collections("//textarea[@name='developer-comment']")->single());
		if ($kind === 'crash-report') {
			$this->assertCount(1, $next->html->path()->collections("//a[@download='crash-report-1.json'][starts-with(@href,'data:application/json;base64,')]"));
		} else {
			$this->assertCount(1, $next->html->path()->collections("//section[@class='markdown']//strong[text()='feedback']"));
		}
	}

	public function test_mail_success_redirect_without_external_delivery(): void
	{
		$logic = $this->createMock(ManagementMailSendLogic::class);
		$logic->expects($this->once())->method('run')->willReturn(true);
		$actual = $this->call(HttpMethod::Post, '/management/mail-send', $this->options([
			'mail_to' => 'test@example.com', 'mail_subject' => 'Subject', 'mail_body' => 'Body',
		]), function (ItSetup $setup) use ($logic) {
			$this->seed($setup);
			$setup->container->remove(ManagementMailSendLogic::class);
			$setup->container->add(ManagementMailSendLogic::class, DiItem::factory(fn() => $logic));
		});
		$this->assertRedirectPath(HttpStatus::Found, 'management/mail-send', null, $actual);
	}

	public function test_index_links_and_maintenance_forms(): void
	{
		$actual = $this->call(HttpMethod::Get, '/management', $this->options(), $this->seed(...));
		foreach (['backup', 'delete-old-data', 'vacuum-access-log', 'cache-rebuild', 'clear-deploy-progress'] as $action) {
			$this->assertCount(1, $actual->html->path()->collections("//form[@method='post'][@action='/management/$action']//input[@type='hidden']"));
		}
		foreach (['database-maintenance/default', 'database-maintenance/session', 'database-download/default', 'database-download/session', 'control/user', 'control/backup'] as $path) {
			$this->assertCount(1, $actual->html->path()->collections("//*[@id='content']//a[@href='/management/$path']"));
		}
	}

	public function test_mail_upload_and_required_inputs(): void
	{
		$actual = $this->call(HttpMethod::Get, '/management/mail-send', $this->options(), $this->seed(...));
		$this->assertCount(1, $actual->html->path()->collections("//form[@enctype='multipart/form-data']"));
		$this->assertCount(1, $actual->html->path()->collections("//input[@type='file'][@name='mail_attachment'][not(@value)]"));
		foreach (['mail_to', 'mail_subject', 'mail_body'] as $name) {
			$this->assertCount(1, $actual->html->path()->collections("//*[@name='$name'][@required]"));
		}
	}

	public function test_configuration_editor_attributes(): void
	{
		$actual = $this->call(HttpMethod::Get, '/management/configuration/edit', $this->options(), $this->seed(...));
		$this->assertCount(1, $actual->html->path()->collections("//textarea[@name='json'][@class='edit json'][@autofocus][@required]"));
		$this->assertNotSame('', $actual->html->path()->collections("//textarea[@name='json']")->single()->raw->textContent);
	}

	public function test_category_javascript_contract(): void
	{
		$actual = $this->call(HttpMethod::Get, '/management/plugin-category', $this->options(), $this->seed(...));
		$this->assertCount(1, $actual->html->path()->collections("//tbody[@id='category_items']/tr[@data-plugin-category-id='test-category']//input[@readonly][@value='test-category']"));
		$this->assertCount(1, $actual->html->path()->collections("//input[@name='display-name'][@value='Category <test>']"));
		$this->assertCount(1, $actual->html->path()->collections("//input[@name='description'][@value='\"<&']"));
		foreach (['category_add_id', 'category_add_display', 'category_add_description', 'category_add_submit'] as $id) {
			$this->assertCount(1, $actual->html->path()->collections("//*[@id='$id']"));
		}
		foreach (['update', 'delete'] as $name) {
			$this->assertCount(1, $actual->html->path()->collections("//tr[@data-plugin-category-id='test-category']//button[@name='$name']"));
		}
	}

	public function test_version_javascript_contract(): void
	{
		$actual = $this->call(HttpMethod::Get, '/management/version', $this->options(), $this->seed(...));
		$this->assertCount(1, $actual->html->path()->collections("//button[@id='command_get_versions'][@type='button'][@data-release_url]"));
		$this->assertCount(1, $actual->html->path()->collections("//input[@name='version'][@pattern='[0-9]+\.[0-9]+\.[0-9]+']"));
		$this->assertTextNode('未取得', $actual->html->path()->collections("//pre[@id='versions']")->single());
	}

	#[DataProvider('provider_report')]
	public function test_report_list_contract(string $kind): void
	{
		$actual = $this->call(HttpMethod::Get, "/management/$kind", $this->options(), $this->seed(...));
		$this->assertCount(25, $actual->html->path()->collections("//table/tbody/tr"));
		$this->assertCount(25, $actual->html->path()->collections("//button[@class='pg-delete'][@data-sequence]"));
		$this->assertCount(25, $actual->html->path()->collections("//time[@datetime][contains(text(),'+09:00')]"));
		$this->assertCount(1, $actual->html->path()->collections("//template[@id='pg-delete-dialog']//div[@class='dialog $kind-delete']"));
		$this->assertCount(1, $actual->html->path()->collections("//ul[@class='pagination']//a[@href='/management/$kind/page/2'][text()='2']"));
		$this->assertTextNode('26', $actual->html->path()->collections("//em[@class='count']")->single());
	}

	#[DataProvider('provider_report')]
	public function test_empty_report_list(string $kind): void
	{
		$actual = $this->call(HttpMethod::Get, "/management/$kind", $this->options(), function (ItSetup $setup) {
			$this->seed($setup);
			$setup->databaseContext->execute('delete from feedbacks');
			$setup->databaseContext->execute('delete from crash_reports');
		});
		$this->assertNode($actual);
		$this->assertTextNode('データなし', $actual->html->path()->collections("//p[@class='search-not-found']")->single());
		$this->assertCount(0, $actual->html->path()->collections("//table"));
	}

	public function test_database_error_is_displayed(): void
	{
		$actual = $this->call(HttpMethod::Post, '/management/database-maintenance/default', $this->options([
			'database_maintenance_statement' => 'select * from missing_test_table',
		]), function (ItSetup $setup) {
			$this->seed($setup);
			$logic = $this->createStub(ManagementDatabaseMaintenanceLogic::class);
			$logic->method('run')->willReturn(true);
			$logic->method('getViewData')->willReturn(new TemplateParameter(HttpStatus::OK, [
				'database' => 'default', 'tables_open' => 'false', 'tables' => [],
				'database_maintenance_statement' => 'select * from missing_test_table',
				'executed' => true, 'result' => new \RuntimeException('missing_test_table <error>'),
			], []));
			$setup->container->remove(ManagementDatabaseMaintenanceLogic::class);
			$setup->container->add(ManagementDatabaseMaintenanceLogic::class, DiItem::factory(fn() => $logic));
		});
		$this->assertNode($actual);
		$this->assertTextNode('結果: エラー', $actual->html->path()->collections("//h2[text()='結果: エラー']")->single());
		$this->assertCount(1, $actual->html->path()->collections("//pre[contains(text(),'missing_test_table')]"));
	}

	public function test_log_and_backup_links(): void
	{
		$logs = $this->call(HttpMethod::Get, '/management/log', $this->options(), $this->seed(...));
		$this->assertCount(1, $logs->html->path()->collections("//button[@class='pg-delete'][@data-name='test.log']"));
		$this->assertCount(1, $logs->html->path()->collections("//a[@href='/management/log/test.log']"));
		$log = $this->call(HttpMethod::Get, '/management/log/test.log', $this->options(), $this->seed(...));
		$this->assertTextNode('Log <test>', $log->html->path()->collections("//pre[@data-clipboard='block']")->single());
		$this->assertCount(1, $log->html->path()->collections("//form[@method='post'][not(@action)]"));
		$this->assertCsrf($log);
		$backups = $this->call(HttpMethod::Get, '/management/control/backup', $this->options(), $this->seed(...));
		$this->assertCount(1, $backups->html->path()->collections("//a[@href='/management/control/backup/test.zip']"));
	}

	public static function provider_download(): array
	{
		return [
			[HttpMethod::Get, '/management/database-download/default', ManagementDatabaseDownloadLogic::class, false],
			[HttpMethod::Get, '/management/control/backup/test.zip', ManagementControlBackupDownloadLogic::class, false],
			[HttpMethod::Get, '/management/log/test.log', ManagementLogDetailLogic::class, true],
			[HttpMethod::Post, '/management/log/test.log', ManagementLogDetailLogic::class, false],
		];
	}

	#[DataProvider('provider_download')]
	public function test_download_stays_binary(HttpMethod $method, string $path, string $class, bool $automatic): void
	{
		$logic = $this->createMock($class);
		$logic->expects($this->once())->method('run')->willReturn(true);
		$logic->expects($this->once())->method('getContent')->willReturn(new DownloadDataContent(Mime::ZIP, 'test.zip', new Binary('test-zip')));
		if ($automatic) {
			$logic->method('equalsResult')->willReturn(true);
		}
		$actual = $this->call($method, $path, $this->options(), function (ItSetup $setup) use ($class, $logic) {
			$this->seed($setup);
			$setup->container->remove($class);
			$setup->container->add($class, DiItem::factory(fn() => $logic));
		});
		$this->assertStatusOK($actual);
		$this->assertMime(Mime::ZIP, $actual);
		$this->assertFalse($actual->isHtml());
		$this->assertStringContainsString('test.zip', $actual->response->header->getValues('Content-Disposition')[0]);
	}

	public function test_crash_report_payload_and_email(): void
	{
		$actual = $this->call(HttpMethod::Get, '/management/crash-report/1', $this->options(), function (ItSetup $setup) {
			$this->seed($setup);
			$crypto = $setup->container->get(AppCryptography::class);
			$setup->databaseContext->execute('update crash_reports set email = :email, comment = :comment, report = :report where sequence = 1', [
				'email' => $crypto->encrypt('test@example.com'),
				'comment' => 'Comment <test>',
				'report' => Archiver::compressGzip(new Binary('{"test":"report"}'))->raw,
			]);
		});
		$this->assertCount(1, $actual->html->path()->collections("//code[text()='test@example.com']"));
		$this->assertCount(1, $actual->html->path()->collections("//a[@download='crash-report-1.json'][@href='data:application/json;base64," . base64_encode('{"test":"report"}') . "']"));
		$this->assertCount(0, $actual->html->path()->collections("//test"));
	}
}
