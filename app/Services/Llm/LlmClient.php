<?php

namespace App\Services\Llm;

interface LlmClient
{
    /**
     * @param  list<array{title: string, body: string}>  $sources
     */
    public function answer(string $question, array $sources): string;
}
