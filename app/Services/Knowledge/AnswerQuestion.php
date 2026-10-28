<?php

namespace App\Services\Knowledge;

use App\Models\GapQuery;
use App\Services\Llm\LlmClient;
use Illuminate\Support\Str;
use Throwable;

class AnswerQuestion
{
    public function __construct(
        private readonly TfidfRetriever $retriever,
        private readonly LlmClient $llm,
    ) {}

    /**
     * @return array{
     *     type: 'answer'|'gap', answer: string|null,
     *     citations: list<array{title: string, score: float}>,
     *     suggested_title: string|null, top_score: float
     * }
     */
    public function handle(string $question): array
    {
        $retrieval = $this->retriever->search($question);
        $threshold = (float) config('knowledge.retrieval_threshold', 0.12);

        if ($retrieval['score'] < $threshold) {
            $title = 'Guide: '.Str::title(trim($question, " \t\n\r\0\x0B?!.,"));

            GapQuery::query()->create([
                'question' => $question,
                'top_score' => $retrieval['score'],
                'answered' => false,
                'suggested_title' => $title,
            ]);

            return [
                'type' => 'gap',
                'answer' => "I don't know based on the Harbor Desk help articles.",
                'citations' => [],
                'suggested_title' => $title,
                'top_score' => $retrieval['score'],
            ];
        }

        $sources = array_map(fn (array $chunk): array => [
            'title' => $chunk['title'],
            'body' => $chunk['body'],
        ], $retrieval['chunks']);

        try {
            $answer = $this->llm->answer($question, $sources);
        } catch (Throwable) {
            // Preserve a useful, sourced answer if the optional provider is unavailable.
            $answer = $sources[0]['body'] ?? "I don't know based on the Harbor Desk help articles.";
        }

        return [
            'type' => 'answer',
            'answer' => $answer,
            'citations' => array_map(fn (array $chunk): array => [
                'title' => $chunk['title'],
                'score' => $chunk['score'],
            ], $retrieval['chunks']),
            'suggested_title' => null,
            'top_score' => $retrieval['score'],
        ];
    }
}
