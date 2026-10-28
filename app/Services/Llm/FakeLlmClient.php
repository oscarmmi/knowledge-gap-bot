<?php

namespace App\Services\Llm;

class FakeLlmClient implements LlmClient
{
    /**
     * @param  list<array{title: string, body: string}>  $sources
     */
    public function answer(string $question, array $sources): string
    {
        $source = $sources[0] ?? null;

        if ($source === null) {
            return "I don't know based on the Harbor Desk help articles.";
        }

        return $source['body'];
    }
}
