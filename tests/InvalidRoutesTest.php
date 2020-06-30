<?php

class InvalidRoutesTest extends TestCase
{
    /**
     * Verifies that invalid routes return 404.
     */
    public function testInvalidRoutes() {
        $this->json('GET', '/invalid')
            ->seeJson([
                'status' => 404,
                'error' => [
                    'message' => 'That resource could not be found.'
                ]
            ]);
    }
}
