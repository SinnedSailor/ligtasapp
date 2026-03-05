<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * @internal
 */
final class AdminBackupTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected function tearDown(): void
    {
        parent::tearDown();
        // remove timestamp file if present so each test starts clean
        $path = WRITEPATH . 'backup_timestamp';
        if (is_file($path)) {
            unlink($path);
        }
    }

    public function testBackupPageShowsNeverWhenNoBackup(): void
    {
        if (! extension_loaded('sqlite3')) {
            $this->markTestSkipped('SQLite3 extension not available');
        }

        // no timestamp file should exist
        $path = WRITEPATH . 'backup_timestamp';
        if (is_file($path)) {
            unlink($path);
        }

        $this->withSession([
            'logged_in' => true,
            'is_admin'  => true,
            'role_name' => 'ADMIN',
            'user_id'   => 1,
        ]);

        $response = $this->get('/admin/backup');
        $response->assertOK();
        // ensure 'never' appears in the markup
        $response->assertSee('Last backed up: <em>never</em>');
    }

    public function testExportBackupWritesTimestampAndShowsOnPage(): void
    {
        if (! extension_loaded('sqlite3')) {
            $this->markTestSkipped('SQLite3 extension not available');
        }

        // ensure no timestamp file before
        $path = WRITEPATH . 'backup_timestamp';
        if (is_file($path)) {
            unlink($path);
        }

        $this->withSession([
            'logged_in' => true,
            'is_admin'  => true,
            'role_name' => 'ADMIN',
            'user_id'   => 1,
        ]);

        $response = $this->get('/admin/backup/export');
        // should return JSON content and attachment header
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertHeader('Content-Disposition');
        $json = json_decode($response->getBody(), true);
        // should have an array representing the admin user
        $this->assertIsArray($json);
        $this->assertArrayHasKey('username', $json);
        $this->assertSame('admin', $json['username']);

        // timestamp file should now exist and be recent
        $this->assertFileExists($path);
        $ts = intval(file_get_contents($path));
        $this->assertGreaterThan(time() - 5, $ts); // within last 5 seconds

        // when we visit the page again we should see a formatted date instead of 'never'
        $response2 = $this->get('/admin/backup');
        $response2->assertOK();
        $this->assertStringNotContainsString('never', $response2->getBody());
        $this->assertStringContainsString('Last backed up:', $response2->getBody());
    }
}
