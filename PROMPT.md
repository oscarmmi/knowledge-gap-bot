Build Knowledge Gap Bot in THIS folder: C:\xampp\htdocs\knowledge-gap-bot

A recruiter-ready demo: a support FAQ chatbot that answers from a small doc set, and flags questions that have no matching chunk as knowledge gaps.

Laravel 13 + Vue 3 starter kit is already here if present. Reuse it. Do not create a second app inside NewPortfolio.

## Must satisfy

- Models: `DocChunk` (title, body), `GapQuery` (question, top_score, answered, suggested_title nullable).
- Retrieval: TF-IDF cosine over seeded Harbor Desk docs. If top score is below a documented threshold, do not call the LLM to invent an answer — store a GapQuery and return "I don't know" plus a suggested missing-doc title. If above threshold, optional LLM answer with citations. Timeout 12s. Never 500.
- `App\Services\Llm\LlmClient` with `FakeLlmClient` in tests and when no `GEMINI_API_KEY`. Optional Gemini when key is set.
- Seed 6 fictional Harbor Desk help articles. English only. No real customers.
- UI: ask a question, show answer+citations or a gap card; list recorded gaps.
- Pest: an in-corpus question returns a citation; an out-of-corpus question creates a GapQuery and does not claim a sourced answer; FakeLlm bound; guest POST 200.
- README mermaid: Question → retrieve → answer with cites OR gap. How to run without API key.

## GitHub

- git config user.name "Oscar Munoz" / user.email "oscarxxi@gmail.com"
- Commit: feat: add knowledge gap bot
- Public https://github.com/oscarmmi/knowledge-gap-bot
- gh repo edit --description "FAQ chatbot that cites Harbor Desk docs and records unanswered questions as knowledge gaps (Laravel + Vue)." --add-topic laravel --add-topic vue --add-topic ai --add-topic php --add-topic chatbot
- Stop. Print the URL.

No Impeccable. No Redis. No real PHI.
