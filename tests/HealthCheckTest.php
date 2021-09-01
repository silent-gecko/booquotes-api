<?php

class HealthCheckTest extends TestCase
{
    public function test_homepage_is_functioning()
    {
        $this->get(route('v1.home'))
            ->seeJsonEquals(response()->jsonHealthCheck()->getData(true));
    }
}