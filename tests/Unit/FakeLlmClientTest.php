<?php

use App\Services\Llm\FakeLlmClient;

it('answers only from the first retrieved article when offline', function (): void {
    $client = new FakeLlmClient;

    expect($client->answer('How do I reset my password?', [
        ['title' => 'Password reset', 'body' => 'Choose Forgot password to get a reset link.'],
        ['title' => 'Returns', 'body' => 'Unused items can be returned within 30 days.'],
    ]))->toBe('Choose Forgot password to get a reset link.');
});

it('does not invent an answer when no source articles are supplied', function (): void {
    expect((new FakeLlmClient)->answer('Unsupported question', []))
        ->toBe("I don't know based on the Harbor Desk help articles.");
});
