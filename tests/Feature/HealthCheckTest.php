<?php

it('returns a successful health check response', function () {
    $response = $this->getJson('/api/health');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
        ])
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'status',
                'timestamp',
                'service',
                'environment',
            ],
        ]);
});
