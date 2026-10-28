<?php

namespace App\Services\Knowledge;

use App\Models\DocChunk;

class TfidfRetriever
{
    private const STOP_WORDS = [
        'about', 'after', 'again', 'also', 'and', 'any', 'are', 'can', 'could',
        'does', 'for', 'from', 'have', 'how', 'into', 'its', 'more', 'not',
        'our', 'please', 'the', 'their', 'there', 'this', 'what', 'when', 'where',
        'which', 'who', 'why', 'with', 'you', 'your', 'would', 'i', 'do', 'is',
        'my', 'me', 'to', 'a', 'an', 'of', 'in', 'on', 'at', 'it',
    ];

    /**
     * Retrieve the best matching articles using TF-IDF weighted cosine similarity.
     * Scores range from 0 to 1; config/knowledge.php documents the 0.12 answer threshold.
     *
     * @return array{score: float, chunks: list<array{title: string, body: string, score: float}>}
     */
    public function search(string $question): array
    {
        $documents = DocChunk::query()->get(['title', 'body'])->map(fn (DocChunk $chunk): array => [
            'title' => $chunk->title,
            'body' => $chunk->body,
        ])->all();

        if ($documents === []) {
            return ['score' => 0.0, 'chunks' => []];
        }

        $documentTerms = array_map(
            fn (array $document): array => $this->termFrequencies($document['title'].' '.$document['body']),
            $documents,
        );
        $queryTerms = $this->termFrequencies($question);
        $documentFrequency = [];

        foreach ($documentTerms as $terms) {
            foreach (array_keys($terms) as $term) {
                $documentFrequency[$term] = ($documentFrequency[$term] ?? 0) + 1;
            }
        }

        $idf = [];

        foreach ($documentFrequency as $term => $frequency) {
            $idf[$term] = log((count($documents) + 1) / ($frequency + 1)) + 1;
        }

        $queryVector = $this->weightedVector($queryTerms, $idf);
        $ranked = [];

        foreach ($documents as $index => $document) {
            $documentVector = $this->weightedVector($documentTerms[$index], $idf);
            $score = $this->cosineSimilarity($queryVector, $documentVector);

            if ($score > 0) {
                $ranked[] = [
                    'title' => $document['title'],
                    'body' => $document['body'],
                    'score' => $score,
                ];
            }
        }

        usort($ranked, fn (array $left, array $right): int => $right['score'] <=> $left['score']);
        $matches = array_slice($ranked, 0, 2);

        return [
            'score' => $matches[0]['score'] ?? 0.0,
            'chunks' => $matches,
        ];
    }

    /** @return array<string, float> */
    private function termFrequencies(string $text): array
    {
        $tokens = preg_split('/[^a-z0-9]+/', strtolower($text), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $tokens = array_values(array_filter(
            $tokens,
            fn (string $token): bool => strlen($token) > 2 && ! in_array($token, self::STOP_WORDS, true),
        ));
        $counts = array_count_values($tokens);
        $length = max(count($tokens), 1);

        return array_map(fn (int $count): float => $count / $length, $counts);
    }

    /**
     * @param  array<string, float>  $frequencies
     * @param  array<string, float>  $idf
     * @return array<string, float>
     */
    private function weightedVector(array $frequencies, array $idf): array
    {
        $vector = [];

        foreach ($frequencies as $term => $frequency) {
            if (isset($idf[$term])) {
                $vector[$term] = $frequency * $idf[$term];
            }
        }

        return $vector;
    }

    /**
     * @param  array<string, float>  $left
     * @param  array<string, float>  $right
     */
    private function cosineSimilarity(array $left, array $right): float
    {
        $dotProduct = 0.0;

        foreach ($left as $term => $weight) {
            $dotProduct += $weight * ($right[$term] ?? 0.0);
        }

        $leftMagnitude = sqrt(array_sum(array_map(fn (float $weight): float => $weight ** 2, $left)));
        $rightMagnitude = sqrt(array_sum(array_map(fn (float $weight): float => $weight ** 2, $right)));

        if ($leftMagnitude === 0.0 || $rightMagnitude === 0.0) {
            return 0.0;
        }

        return $dotProduct / ($leftMagnitude * $rightMagnitude);
    }
}
