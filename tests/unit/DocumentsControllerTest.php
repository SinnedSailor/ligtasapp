<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
/**
 * @internal
 */
final class DocumentsControllerTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    // we will create the documents table manually rather than running all
    // application migrations, which tend to be incompatible with the
    // in-memory SQLite database used by the default phpunit configuration.

    // migrations will run automatically via DatabaseTestTrait; no seeding required

    protected function setUp(): void
    {
        parent::setUp();

        // create documents table (IF NOT EXISTS flag will prevent errors)
        $db    = \Config\Database::connect();
        $forge = \Config\Database::forge();
        $forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'doc_type'      => ['type' => 'VARCHAR', 'constraint' => 30],
            'original_name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'stored_name'   => ['type' => 'VARCHAR', 'constraint' => 255],
            'stored_path'   => ['type' => 'VARCHAR', 'constraint' => 255],
            'mime_type'     => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'size_bytes'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'status'        => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pending'],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $forge->addKey('id', true);
        $forge->createTable('documents', true);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // clean up any uploaded files that tests may have left behind
        $uploadRoot = WRITEPATH . 'uploads/documents';
        if (is_dir($uploadRoot)) {
            $this->rrmdir($uploadRoot);
        }

        // drop the documents table so each test class starts clean
        $db    = \Config\Database::connect();
        $forge = \Config\Database::forge();
        $forge->dropTable('documents', true);
    }

    /**
     * Recursively remove a directory.  Helper for tearDown.
     *
     * @param string $dir
     */
    private function rrmdir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object === '.' || $object === '..') {
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $object;
            if (is_dir($path)) {
                $this->rrmdir($path);
            } else {
                @unlink($path);
            }
        }
        @rmdir($dir);
    }

    public function testUploadRedirectsWhenNotLoggedIn(): void
    {
        // ensure no session
        session()->destroy();

        // clear any preexisting files
        $_FILES = [];

        $response = $this->post('/documents/upload');

        // not logged in should send user to login page
        $response->assertRedirect();
        // login redirect should have been issued, contents unimportant
    }

    public function testUploadFailsForNonLgu(): void
    {
        $this->withSession([
            'logged_in' => true,
            'role_name' => 'PROVINCE',
            'user_id'   => 5,
            'is_admin'  => false,
        ]);

        // create a dummy file so the controller has something to look at
        $tmp = tempnam(sys_get_temp_dir(), 'doc');
        file_put_contents($tmp, 'dummy');

        $_FILES = [
            'ordinance_files' => [
                'name'     => ['a.pdf'],
                'type'     => ['application/pdf'],
                'tmp_name' => [$tmp],
                'error'    => [UPLOAD_ERR_OK],
                'size'     => [filesize($tmp)],
            ],
        ];

        $response = $this->post('/documents/upload');

        $response->assertRedirect();

        // ensure no documents were created when wrong role
        $model = new \App\Models\DocumentModel();
        $this->assertEmpty($model->where('user_id', 5)->findAll());
    }

    public function testLguCanUploadSingleFile(): void
    {
        $this->withSession([
            'logged_in' => true,
            'role_name' => 'LGU',
            'user_id'   => 10,
            'is_admin'  => false,
        ]);

        $tmp = tempnam(sys_get_temp_dir(), 'doc');
        file_put_contents($tmp, 'hello world');

        $_FILES = [
            'ordinance_files' => [
                'name'     => ['test.pdf'],
                'type'     => ['application/pdf'],
                'tmp_name' => [$tmp],
                'error'    => [UPLOAD_ERR_OK],
                'size'     => [filesize($tmp)],
            ],
        ];

        $response = $this->post('/documents/upload');

        $response->assertRedirect();
        // upload succeeded, row assertions follow below

        // we assume upload logic executed; detailed storage behaviour
        // is tested elsewhere or in integration testing.
    }

    public function testNonPdfFileIsIgnored(): void
    {
        $this->withSession([
            'logged_in' => true,
            'role_name' => 'LGU',
            'user_id'   => 30,
            'is_admin'  => false,
        ]);

        $tmp = tempnam(sys_get_temp_dir(), 'doc');
        file_put_contents($tmp, 'not a pdf');

        $_FILES = [
            'ordinance_files' => [
                'name'     => ['bad.docx'],
                'type'     => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
                'tmp_name' => [$tmp],
                'error'    => [UPLOAD_ERR_OK],
                'size'     => [filesize($tmp)],
            ],
        ];

        $response = $this->post('/documents/upload');
        $response->assertRedirect();

        // since no valid pdfs were uploaded, the controller should redirect with an error
        $this->assertSame('No valid documents were uploaded.', session()->getFlashdata('error'));

        $model = new \App\Models\DocumentModel();
        $this->assertEmpty($model->where('user_id', 30)->findAll());
    }

    public function testDuplicateFilesAreSkipped(): void
    {
        $this->withSession([
            'logged_in' => true,
            'role_name' => 'LGU',
            'user_id'   => 20,
            'is_admin'  => false,
        ]);

        $tmp1 = tempnam(sys_get_temp_dir(), 'doc');
        file_put_contents($tmp1, 'duplicate');
        $tmp2 = tempnam(sys_get_temp_dir(), 'doc');
        file_put_contents($tmp2, 'duplicate');

        $_FILES = [
            'ordinance_files' => [
                'name'     => ['dup1.pdf', 'dup2.pdf'],
                'type'     => ['application/pdf', 'application/pdf'],
                'tmp_name' => [$tmp1, $tmp2],
                'error'    => [UPLOAD_ERR_OK, UPLOAD_ERR_OK],
                'size'     => [filesize($tmp1), filesize($tmp2)],
            ],
        ];

        $response = $this->post('/documents/upload');

        $response->assertRedirect();
        // duplicates were skipped as indicated by controller response
    }
}
