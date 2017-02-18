<?php

class StringHelperTest extends TestCase
{
    protected $studlyTests = [
        'This is a string' => 'ThisIsAString',
        'another test string' => 'AnotherTestString',
        'This_is_a_test_string' => 'ThisIsATestString',
        'this-is-another-test-string' => 'ThisIsAnotherTestString',
        'Th15-Str1ng_cont41n5-numb3r5' => 'Th15Str1ngCont41n5Numb3r5'
    ];

    protected $randomAlphabets = [
        'abcDEF', 'ghi123', ';\'#[],./{}:@~<>?'
    ];

    /**
     * Test the studly method of the string helper.
     */
    public function testStudly()
    {
        $stringHelper = $this->createMiniphyInstance()->getStringHelper();
        foreach ($this->studlyTests as $input => $output) {
            $this->assertEquals($output, $stringHelper->studly($input));
        }
    }

    /**
     * Test the random method of the string helper.
     */
    public function testRandom()
    {
        $stringHelper = $this->createMiniphyInstance()->getStringHelper();
        foreach ($this->randomAlphabets as $alphabet) {
            $random = $stringHelper->random(12, $alphabet);
            for ($i = 0, $length = mb_strlen($random); $i < $length; $i++) {
                $this->assertContains($random[$i], $alphabet);
            }
        }
    }
}
