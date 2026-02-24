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
}
