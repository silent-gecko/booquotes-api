<?php

namespace Unit\Rules;

use App\Rules\Year;
use Illuminate\Support\Carbon;

class YearTest extends \TestCase
{
    protected $rule;

    public function setUp(): void
    {
        parent::setUp();
        $this->rule = new Year();
    }

    public function test_rule_pass_when_valid()
    {
        $validYear = 2005;

        $validated = $this->rule->passes('year', $validYear);

        $this->assertTrue($validated);
    }

    public function test_rule_fails_when_non_numeric()
    {
        $invalidYear = '1506 e.g.';

        $validated = $this->rule->passes('year', $invalidYear);

        $this->assertFalse($validated);
    }

    public function test_rule_fails_when_negative()
    {
        $invalidYear = -1506;

        $validated = $this->rule->passes('year', $invalidYear);

        $this->assertFalse($validated);
    }

    public function test_rule_fails_when_overtime()
    {
        $invalidYear = Carbon::now()->year + 1;

        $validated = $this->rule->passes('year', $invalidYear);

        $this->assertFalse($validated);
    }
}