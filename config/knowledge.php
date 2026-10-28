<?php

return [
    // Cosine scores below this value are treated as an unsupported question.
    'retrieval_threshold' => (float) env('KNOWLEDGE_RETRIEVAL_THRESHOLD', 0.12),
    'llm_timeout' => 12,
    'gemini_model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
];
