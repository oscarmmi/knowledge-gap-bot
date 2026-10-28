<?php

namespace App\Http\Controllers;

use App\Models\DocChunk;
use App\Models\GapQuery;
use App\Services\Knowledge\AnswerQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class KnowledgeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Welcome', [
            'docCount' => DocChunk::query()->count(),
            'gaps' => GapQuery::query()->latest()->limit(8)->get([
                'id', 'question', 'top_score', 'suggested_title', 'created_at',
            ]),
            'result' => null,
            'question' => '',
            'threshold' => (float) config('knowledge.retrieval_threshold', 0.12),
        ]);
    }

    public function ask(Request $request, AnswerQuestion $answerQuestion): Response|RedirectResponse
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'min:4', 'max:1000'],
        ]);

        $result = $answerQuestion->handle($validated['question']);

        return Inertia::render('Welcome', [
            'docCount' => DocChunk::query()->count(),
            'gaps' => GapQuery::query()->latest()->limit(8)->get([
                'id', 'question', 'top_score', 'suggested_title', 'created_at',
            ]),
            'result' => $result,
            'question' => $validated['question'],
            'threshold' => (float) config('knowledge.retrieval_threshold', 0.12),
        ]);
    }
}
