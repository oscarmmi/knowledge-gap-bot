<?php

use App\Models\GapQuery;
use App\Services\Llm\FakeLlmClient;
use App\Services\Llm\LlmClient;
use Database\Seeders\DatabaseSeeder;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function (): void {
    $this->seed(DatabaseSeeder::class);
});

it('answers an in-corpus question with a citation', function (): void {
    $this->post('/ask', ['question' => 'How do I reset my Harbor Desk password?'])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->where('result.type', 'answer')
            ->where('result.citations.0.title', 'Reset your Harbor Desk password')
            ->where('result.answer', fn (string $answer): bool => str_contains($answer, 'Forgot password'))
        );

    expect(GapQuery::query()->count())->toBe(0);
});

it('records an out-of-corpus question without generating an answer from the llm', function (): void {
    $llm = Mockery::mock(LlmClient::class);
    $llm->shouldNotReceive('answer');
    $this->app->instance(LlmClient::class, $llm);

    $this->post('/ask', ['question' => 'Do you offer kayak tours in Iceland?'])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->where('result.type', 'gap')
            ->where('result.answer', "I don't know based on the Harbor Desk help articles.")
            ->where('result.citations', [])
            ->where('result.suggested_title', 'Guide: Do You Offer Kayak Tours In Iceland')
        );

    $gap = GapQuery::query()->sole();
    expect($gap->question)->toBe('Do you offer kayak tours in Iceland?')
        ->and($gap->answered)->toBeFalse()
        ->and($gap->suggested_title)->toContain('Kayak Tours');
});

it('binds the fake llm client during tests', function (): void {
    expect(app(LlmClient::class))->toBeInstanceOf(FakeLlmClient::class);
});

it('falls back to the retrieved article when the optional llm fails', function (): void {
    $llm = Mockery::mock(LlmClient::class);
    $llm->shouldReceive('answer')->once()->andThrow(new RuntimeException('Provider timeout'));
    $this->app->instance(LlmClient::class, $llm);

    $this->post('/ask', ['question' => 'How do I reset my Harbor Desk password?'])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('result.type', 'answer')
            ->where('result.citations.0.title', 'Reset your Harbor Desk password')
            ->where('result.answer', fn (string $answer): bool => str_contains($answer, 'Forgot password'))
        );
});

it('allows guests to submit a question successfully', function (): void {
    $this->post('/ask', ['question' => 'Where can I track a shipment?'])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->where('result.type', 'answer')
        );
});
