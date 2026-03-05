<?php

use CodeIgniter\Test\CIUnitTestCase;
use App\Controllers\IncidentReport;

/**
 * @internal
 */
final class IncidentReportControllerTest extends CIUnitTestCase
{
    public function testNormalizeGender(): void
    {
        $ctrl = new IncidentReport();
        $ref = new ReflectionMethod($ctrl, 'normalizeGender');
        $ref->setAccessible(true);

        $this->assertSame('m', $ref->invoke($ctrl, 'Male'));
        $this->assertSame('m', $ref->invoke($ctrl, 'm'));
        $this->assertSame('m', $ref->invoke($ctrl, 'MALE'));
        $this->assertSame('f', $ref->invoke($ctrl, 'Female'));
        $this->assertSame('f', $ref->invoke($ctrl, 'F'));
        $this->assertSame('f', $ref->invoke($ctrl, 'female'));
        // unrecognized values should be returned unchanged (trimmed/lowercased)
        $this->assertSame('other', $ref->invoke($ctrl, 'other'));
        $this->assertSame('', $ref->invoke($ctrl, ''));
        // null inputs are cast to string which yields empty string
        $this->assertSame('', $ref->invoke($ctrl, null));
    }

    public function testModelNormalisesLocationAndOccasion(): void
    {
        $model = new \App\Models\IncidentReportModel();
        $ref = new \ReflectionMethod($model, 'normaliseLocationCategory');
        $ref->setAccessible(true);

        $data = ['data' => ['location_category' => '  dam ', 'occasion' => 'summer vacation   ', 'occupation' => ' farmer  ']];
        $result = $ref->invoke($model, $data);

        $this->assertSame('Dam', $result['data']['location_category']);
        $this->assertSame('Summer Vacation', $result['data']['occasion']);
        $this->assertSame('Farmer', $result['data']['occupation']);

        // ensure missing keys don't break anything
        $this->assertSame(['data' => []], $ref->invoke($model, ['data' => []]));
    }

    public function testMapRowHandlesSexColumn(): void
    {
        $ctrl = new IncidentReport();
        $ref = new ReflectionMethod($ctrl, 'mapRow');
        $ref->setAccessible(true);

        $variants = [
            'Female' => 'f',
            'female' => 'f',
            'FEMALE' => 'f',
            'F' => 'f',
            'f' => 'f',
            'Male' => 'm',
            'male' => 'm',
            'MALE' => 'm',
            'M' => 'm',
            'm' => 'm',
            '' => '',
            null => '',
        ];

        foreach ($variants as $inputVal => $expected) {
            // test both raw Sex header and canonical header produced by JS
            $input1 = ['Sex' => $inputVal, 'Month of Incident' => '1', 'Year' => '2023',
                'Province' => 'X', 'Municipality' => 'Y'];
            $input2 = ['Gender of the Person' => $inputVal, 'Month of Incident' => '1', 'Year' => '2023',
                'Province' => 'X', 'Municipality' => 'Y'];
            foreach ([$input1, $input2] as $input) {
                $mapped = $ref->invoke($ctrl, $input, 0);
                $this->assertSame($expected, $mapped['gender'], "Failed mapping '{".json_encode($input)."}'");
                $this->assertSame('1', $mapped['month_of_incident']);
                $this->assertSame(2023, $mapped['year_of_incident']);
            }
        }
    }

    public function testMapRowHandlesLocationName(): void
    {
        $ctrl = new IncidentReport();
        $ref = new ReflectionMethod($ctrl, 'mapRow');
        $ref->setAccessible(true);

    }

    public function testDefaultSortIsN(): void
    {
        // render the view with no initial rows and ensure the JS sets the initial
        // sort column to "N" so the table appears ordered by incident number.
        $output = view('incident_report', ['initialRows' => []]);
        $this->assertStringContainsString("setSort('N')", $output);
        // province/municipality columns should be present in the table header
        $this->assertStringContainsString('>Province<', $output);
        $this->assertStringContainsString('>Municipality/City<', $output);
        // new review note column header should also appear
        $this->assertStringContainsString('>Review Note<', $output);
        // JS variable for initial categories should exist (even if empty)
        $this->assertStringContainsString('initialLocationCategories', $output);
        // JS variable for occasions should also be defined now
        $this->assertStringContainsString('initialOccasions', $output);
        // JS variable for occupations should also be provided
        $this->assertStringContainsString('initialOccupations', $output);
        // the form should include datalist elements for all three fields
        $this->assertStringContainsString('datalist', $output);
        $this->assertStringContainsString('locationCategoryList', $output);
        $this->assertStringContainsString('occasionList', $output);
        $this->assertStringContainsString('occupationList', $output);
    }
    public function testViewShowsReviewNoteValue(): void
    {
        // the review note column should render the actual note text if provided
        $rows = [['n' => 42, 'review_note' => 'Reason explained']];
        $output = view('incident_report', ['initialRows' => $rows]);
        $this->assertStringContainsString('Reason explained', $output);
    }
    public function testFocalViewHidesReviewAndActions(): void
    {
        // ensure the role_name in session is set so the view understands we're a focal user
        $session = session();
        $session->set('role_name', 'FOCAL');
        $output = view('incident_report', ['initialRows' => []]);

        // the table headers for review/actions should not be present
        $this->assertStringNotContainsString('>Review<', $output);
        $this->assertStringNotContainsString('>Actions<', $output);

        // attachments header should still be there
        $this->assertStringContainsString('>Attachments<', $output);
        // province/municipality still visible even for focal users
        $this->assertStringContainsString('>Province<', $output);
        $this->assertStringContainsString('>Municipality/City<', $output);

        // JS side should know the user is focal so it can adjust rendering rules
        $this->assertStringContainsString('const isFocal = true', $output);
    }

    public function testFilterRowsForRoleFocal(): void
    {
        $ctrl = new IncidentReport();
        $ref = new ReflectionMethod($ctrl, 'filterRowsForRole');
        $ref->setAccessible(true);

        $rows = [
            ['n' => 1, 'review_status' => 'approved'],
            ['n' => 2, 'review_status' => 'pending'],
            ['n' => 3, 'review_status' => 'rejected'],
            ['n' => 4, 'review_status' => null],
        ];

        $filtered = $ref->invoke($ctrl, $rows, 'FOCAL');
        $this->assertCount(1, $filtered);
        $this->assertSame(1, $filtered[0]['n']);
    }

    public function testFilterRowsForRoleNonFocal(): void
    {
        $ctrl = new IncidentReport();
        $ref = new ReflectionMethod($ctrl, 'filterRowsForRole');
        $ref->setAccessible(true);

        $rows = [
            ['n' => 1, 'review_status' => 'approved'],
            ['n' => 2, 'review_status' => 'pending'],
        ];

        // other roles should receive the unmodified dataset
        $filtered = $ref->invoke($ctrl, $rows, 'LGU');
        $this->assertSame($rows, $filtered);
    }

    public function testRejectStoresNote(): void
    {
        $ctrl = new IncidentReport();

        // fake model that captures update payload
        $fakeModel = new class extends \App\Models\IncidentReportModel {
            public $lastUpdate;
            public function where($col, $val)
            {
                return $this;
            }
            public function first()
            {
                return ['id' => 7, 'n' => 123, 'province' => 'X'];
            }
            public function update($id, $data)
            {
                $this->lastUpdate = ['id' => $id, 'data' => $data];
            }
        };
        $refProp = new ReflectionProperty($ctrl, 'incidentReportModel');
        $refProp->setAccessible(true);
        $refProp->setValue($ctrl, $fakeModel);

        // simulate logged-in reviewer
        $session = session();
        $session->set('role_name', 'PROVINCE');
        $session->set('is_admin', false);
        $session->set('logged_in', true);
        $session->set('user_id', 55);

        // stub request so getJSON returns note
        $stubReq = new class {
            public function getJSON($assoc = false)
            {
                return ['note' => 'Not suitable data'];
            }
            // other methods may be called by controller but aren't needed for this test
        };
        $refReq = new ReflectionProperty($ctrl, 'request');
        $refReq->setAccessible(true);
        $refReq->setValue($ctrl, $stubReq);

        // response object to satisfy method expectations
        $resp = \Config\Services::response();
        $refResp = new ReflectionProperty($ctrl, 'response');
        $refResp->setAccessible(true);
        $refResp->setValue($ctrl, $resp);

        // invoke private method directly
        $refm = new ReflectionMethod($ctrl, 'updateReviewStatus');
        $refm->setAccessible(true);
        $result = $refm->invoke($ctrl, 123, 'rejected');

        $this->assertSame(['message' => 'Incident updated.', 'status' => 'rejected', 'note' => 'Not suitable data'], json_decode((string) $result->getBody(), true));
        $this->assertSame('Not suitable data', $fakeModel->lastUpdate['data']['review_note']);
        $this->assertSame(55, $fakeModel->lastUpdate['data']['reviewed_by']);
    }

    public function testModelAllowsReviewNoteField(): void
    {
        $model = new \App\Models\IncidentReportModel();
        $ref = new \ReflectionProperty($model, 'allowedFields');
        $ref->setAccessible(true);
        $fields = $ref->getValue($model);
        $this->assertContains('review_note', $fields, 'Allowed fields should include review_note so it can be persisted');
    }

    public function testApproveClearsNote(): void
    {
        $ctrl = new IncidentReport();
        $fakeModel = new class extends \App\Models\IncidentReportModel {
            public $lastUpdate;
            public function where($col, $val)
            {
                return $this;
            }
            public function first()
            {
                return ['id' => 8, 'n' => 456, 'province' => 'Y'];
            }
            public function update($id, $data)
            {
                $this->lastUpdate = ['id' => $id, 'data' => $data];
            }
        };
        $refProp = new ReflectionProperty($ctrl, 'incidentReportModel');
        $refProp->setAccessible(true);
        $refProp->setValue($ctrl, $fakeModel);

        $session = session();
        $session->set('role_name', 'PROVINCE');
        $session->set('is_admin', false);
        $session->set('logged_in', true);
        $session->set('user_id', 99);

        $stubReq = new class {
            public function getJSON($assoc = false)
            {
                return null;
            }
        };
        $refReq = new ReflectionProperty($ctrl, 'request');
        $refReq->setAccessible(true);
        $refReq->setValue($ctrl, $stubReq);

        $resp = \Config\Services::response();
        $refResp = new ReflectionProperty($ctrl, 'response');
        $refResp->setAccessible(true);
        $refResp->setValue($ctrl, $resp);

        $refm = new ReflectionMethod($ctrl, 'updateReviewStatus');
        $refm->setAccessible(true);
        $result = $refm->invoke($ctrl, 456, 'approved');

        $this->assertSame(['message' => 'Incident updated.', 'status' => 'approved'], json_decode((string) $result->getBody(), true));
        $this->assertNull($fakeModel->lastUpdate['data']['review_note']);
        $this->assertSame(99, $fakeModel->lastUpdate['data']['reviewed_by']);
    }

    public function testGenerateReportUsesRoleFilter(): void
    {
        // create controller and inject a fake model that returns mixed rows
        $ctrl = new IncidentReport();
        $fakeModel = new class extends \App\Models\IncidentReportModel {
            public function findAll(?int $limit = null, int $offset = 0)
            {
                return [
                    ['n' => 1, 'review_status' => 'approved', 'month_of_incident' => '1', 'year_of_incident' => 2020, 'province' => 'A', 'municipality' => 'B', 'location_category' => '', 'age' => '', 'gender' => '', 'occasion' => '', 'factors' => '', 'residence' => '', 'occupation' => '', 'remarks' => ''],
                    ['n' => 2, 'review_status' => 'pending',  'month_of_incident' => '1', 'year_of_incident' => 2020, 'province' => 'A', 'municipality' => 'B', 'location_category' => '', 'age' => '', 'gender' => '', 'occasion' => '', 'factors' => '', 'residence' => '', 'occupation' => '', 'remarks' => ''],
                ];
            }
        };
        $refProp = new ReflectionProperty($ctrl, 'incidentReportModel');
        $refProp->setAccessible(true);
        $refProp->setValue($ctrl, $fakeModel);

        // simulate focal user in session
        $session = session();
        $session->set('role_name', 'FOCAL');
        $session->set('logged_in', true);

        // ensure controller has a response object (CI normally injects this)
        $resp = \Config\Services::response();
        $refResp = new ReflectionProperty($ctrl, 'response');
        $refResp->setAccessible(true);
        $refResp->setValue($ctrl, $resp);

        // ensure request is available too (used for query parameters)
        $req = \Config\Services::request();
        $refReq = new ReflectionProperty($ctrl, 'request');
        $refReq->setAccessible(true);
        $refReq->setValue($ctrl, $req);

        $response = $ctrl->generateReport();
        $json = json_decode((string) $response->getBody(), true);
        // only one approved row should be returned
        $this->assertCount(1, $json['data']);
        $this->assertSame(1, $json['data'][0]['n']);
    }

    public function testGenerateReportIncludesReviewNoteForLgu(): void
    {
        $ctrl = new IncidentReport();
        $fakeModel = new class extends \App\Models\IncidentReportModel {
            public function findAll(?int $limit = null, int $offset = 0)
            {
                return [
                    [
                        'n' => 5,
                        'review_status' => 'rejected',
                        'review_note' => 'Insufficient detail',
                        'month_of_incident' => '2',
                        'year_of_incident' => 2022,
                        'province' => 'P',
                        'municipality' => 'M',
                        'location_category' => '',
                        'age' => '',
                        'gender' => '',
                        'occasion' => '',
                        'factors' => '',
                        'residence' => '',
                        'occupation' => '',
                        'remarks' => '',
                    ],
                ];
            }
        };
        $refProp = new ReflectionProperty($ctrl, 'incidentReportModel');
        $refProp->setAccessible(true);
        $refProp->setValue($ctrl, $fakeModel);

        // simulate LGU user in session
        $session = session();
        $session->set('role_name', 'LGU');
        $session->set('logged_in', true);

        $resp = \Config\Services::response();
        $refResp = new ReflectionProperty($ctrl, 'response');
        $refResp->setAccessible(true);
        $refResp->setValue($ctrl, $resp);

        $req = \Config\Services::request();
        $refReq = new ReflectionProperty($ctrl, 'request');
        $refReq->setAccessible(true);
        $refReq->setValue($ctrl, $req);

        $response = $ctrl->generateReport();
        $json = json_decode((string) $response->getBody(), true);
        $this->assertCount(1, $json['data']);
        $this->assertSame('Insufficient detail', $json['data'][0]['review_note']);
    }

    public function testGenerateReportPdf(): void
    {
        $ctrl = new IncidentReport();
        $fakeModel = new class extends \App\Models\IncidentReportModel {
            public function findAll(?int $limit = null, int $offset = 0)
            {
                return [
                    ['n' => 1, 'review_status' => 'approved', 'month_of_incident' => '1', 'year_of_incident' => 2020, 'province' => 'A', 'municipality' => 'B', 'location_category' => '', 'age' => '', 'gender' => '', 'occasion' => '', 'factors' => '', 'residence' => '', 'occupation' => '', 'remarks' => ''],
                ];
            }
        };
        $refProp = new \ReflectionProperty($ctrl, 'incidentReportModel');
        $refProp->setAccessible(true);
        $refProp->setValue($ctrl, $fakeModel);

        $session = session();
        $session->set('role_name', 'LGU');
        $session->set('logged_in', true);

        $resp = \Config\Services::response();
        $refResp = new \ReflectionProperty($ctrl, 'response');
        $refResp->setAccessible(true);
        $refResp->setValue($ctrl, $resp);

        $req = \Config\Services::request();
        $_GET['format'] = 'pdf';
        $refReq = new \ReflectionProperty($ctrl, 'request');
        $refReq->setAccessible(true);
        $refReq->setValue($ctrl, $req);

        $response = $ctrl->generateReport();
        $this->assertSame('application/pdf', $response->getHeaderLine('Content-Type'));
        $body = (string) $response->getBody();
        $this->assertStringStartsWith('%PDF', $body);
    }
}
