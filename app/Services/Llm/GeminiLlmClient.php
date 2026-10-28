<?php

namespace App\Services\Llm;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiLlmClient implements LlmClient
{
    /**
     * @param  list<array{title: string, body: string}>  $sources
     */
    public function answer(string $question, array $sources): string
    {
        $apiKey = (string) config('services.gemini.api_key');
        $model = (string) config('knowledge.gemini_model');

        $sourceText = collect($sources)
            ->map(fn (array $source): string => "[{$source['title']}]\n{$source['body']}")
            ->implode("\n\n");

        $response = Http::timeout((int) config('knowledge.llm_timeout', 12))
            ->connectTimeout(5)
            ->acceptJson()
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                'contents' => [[
                    'parts' => [[
                        'text' => "Answer the question using only the supplied Harbor Desk help articles. If they do not contain the answer, say you do not know. Keep the answer concise.\n\nArticles:\n{$sourceText}\n\nQuestion: {$question}",
                    ]],
                ]],
                'generationConfig' => [
                    'temperature' => 0.2,
                    'maxOutputTokens' => 300,
                ],
            ])
            ->throw();

        $answer = $response->json('candidates.0.content.parts.0.text');

        if (! is_string($answer) || trim($answer) === '') {
            throw new RuntimeException('Gemini returned an empty answer.');
        }

        return trim($answer);
    }
}
