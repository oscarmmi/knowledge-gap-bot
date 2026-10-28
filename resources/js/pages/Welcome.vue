<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';

type Citation = { title: string; score: number };
type Result = {
    type: 'answer' | 'gap';
    answer: string;
    citations: Citation[];
    suggested_title: string | null;
    top_score: number;
};
type Gap = {
    id: number;
    question: string;
    top_score: number;
    suggested_title: string | null;
    created_at: string;
};

const props = defineProps<{
    docCount: number;
    gaps: Gap[];
    result: Result | null;
    question: string;
    threshold: number;
}>();

const form = useForm({ question: props.question });
const suggestions = [
    'How do I reset my password?',
    'Where can I track my shipment?',
    'Can I return an item?',
];

function ask(question = form.question) {
    form.question = question;
    form.post('/ask', {
        preserveScroll: true,
    });
}

function percent(score: number): string {
    return `${Math.round(score * 100)}%`;
}
</script>

<template>
    <Head title="Harbor Desk — Help center" />

    <div class="min-h-screen bg-[#f7f8f5] text-[#17231f]">
        <header class="border-b border-[#e6eae4] bg-white">
            <div
                class="mx-auto flex max-w-7xl items-center justify-between px-5 py-5 sm:px-8"
            >
                <a
                    href="/"
                    class="flex items-center gap-3"
                    aria-label="Harbor Desk home"
                >
                    <span
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#174b3b] text-white"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            class="h-5 w-5"
                            aria-hidden="true"
                        >
                            <path
                                d="M4 8.5 12 4l8 4.5v7L12 20l-8-4.5v-7Z"
                                stroke="currentColor"
                                stroke-width="1.7"
                                stroke-linejoin="round"
                            />
                            <path
                                d="m4.5 8.5 7.5 4.3 7.5-4.3M12 13v6.5"
                                stroke="currentColor"
                                stroke-width="1.7"
                                stroke-linejoin="round"
                            />
                        </svg>
                    </span>
                    <span>
                        <span class="block text-sm font-bold tracking-tight"
                            >Harbor Desk</span
                        >
                        <span class="block text-xs text-[#78847d]"
                            >Customer help center</span
                        >
                    </span>
                </a>
                <div
                    class="hidden items-center gap-2 rounded-full border border-[#e5e9e3] px-3 py-1.5 text-xs font-medium text-[#627168] sm:flex"
                >
                    <span class="h-2 w-2 rounded-full bg-[#51a67a]"></span>
                    Knowledge assistant
                </div>
            </div>
        </header>

        <main
            class="mx-auto grid max-w-7xl gap-10 px-5 py-10 sm:px-8 lg:grid-cols-[minmax(0,1fr)_320px] lg:gap-16 lg:py-16"
        >
            <section class="mx-auto w-full max-w-3xl">
                <div class="mb-8">
                    <p
                        class="mb-3 text-xs font-semibold tracking-[0.16em] text-[#54816b] uppercase"
                    >
                        Harbor Desk support
                    </p>
                    <h1
                        class="max-w-2xl text-4xl leading-tight font-semibold tracking-[-0.04em] sm:text-5xl"
                    >
                        A clear answer,
                        <span class="text-[#688274]">from our help docs.</span>
                    </h1>
                    <p class="mt-4 max-w-xl text-base leading-7 text-[#65736b]">
                        Ask about your account, orders, shipping, or returns.
                        Every answer is grounded in a Harbor Desk article.
                    </p>
                </div>

                <form
                    class="rounded-2xl border border-[#dfe5de] bg-white p-2 shadow-[0_10px_35px_rgba(28,52,40,0.06)]"
                    @submit.prevent="ask()"
                >
                    <label for="question" class="sr-only"
                        >Ask Harbor Desk a question</label
                    >
                    <textarea
                        id="question"
                        v-model="form.question"
                        rows="3"
                        maxlength="1000"
                        placeholder="What can we help you with?"
                        class="block w-full resize-y border-0 bg-transparent px-4 pt-3 pb-2 text-[15px] leading-6 outline-none placeholder:text-[#a0aaa3] focus:ring-0"
                        :aria-invalid="Boolean(form.errors.question)"
                        @keydown.meta.enter.prevent="ask()"
                        @keydown.ctrl.enter.prevent="ask()"
                    ></textarea>
                    <div
                        class="flex flex-wrap items-center justify-between gap-3 border-t border-[#eef0ec] px-3 pt-3 pb-1"
                    >
                        <span class="text-xs text-[#8a958e]"
                            >Answers cite the source article</span
                        >
                        <button
                            type="submit"
                            :disabled="
                                form.processing ||
                                form.question.trim().length < 4
                            "
                            class="inline-flex items-center gap-2 rounded-xl bg-[#174b3b] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#103d30] disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <span>{{
                                form.processing
                                    ? 'Searching…'
                                    : 'Ask a question'
                            }}</span>
                            <svg
                                v-if="!form.processing"
                                viewBox="0 0 20 20"
                                fill="none"
                                class="h-4 w-4"
                                aria-hidden="true"
                            >
                                <path
                                    d="M4 10h12m-5-5 5 5-5 5"
                                    stroke="currentColor"
                                    stroke-width="1.6"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>
                        </button>
                    </div>
                </form>
                <p
                    v-if="form.errors.question"
                    class="mt-2 text-sm text-[#b44435]"
                >
                    {{ form.errors.question }}
                </p>

                <div class="mt-5 flex flex-wrap items-center gap-2">
                    <span class="mr-1 text-xs font-medium text-[#78847d]"
                        >Try asking</span
                    >
                    <button
                        v-for="suggestion in suggestions"
                        :key="suggestion"
                        type="button"
                        class="rounded-full border border-[#e1e7e0] bg-white px-3 py-1.5 text-xs text-[#53645a] transition hover:border-[#a8b9aa] hover:bg-[#f2f6f1]"
                        @click="ask(suggestion)"
                    >
                        {{ suggestion }}
                    </button>
                </div>

                <section v-if="result" class="mt-9" aria-live="polite">
                    <article
                        v-if="result.type === 'answer'"
                        class="overflow-hidden rounded-2xl border border-[#dfe5de] bg-white"
                    >
                        <div
                            class="flex items-center justify-between border-b border-[#edf0ec] px-5 py-4 sm:px-6"
                        >
                            <div class="flex items-center gap-2.5">
                                <span
                                    class="flex h-7 w-7 items-center justify-center rounded-full bg-[#eaf4ec] text-[#357553]"
                                >
                                    <svg
                                        viewBox="0 0 20 20"
                                        fill="none"
                                        class="h-4 w-4"
                                        aria-hidden="true"
                                    >
                                        <path
                                            d="m5 10 3.2 3.2L15.5 6"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </span>
                                <span class="text-sm font-semibold"
                                    >Answer found</span
                                >
                            </div>
                            <span
                                class="rounded-full bg-[#f2f6f1] px-2.5 py-1 text-xs font-medium text-[#547260]"
                                >{{ percent(result.top_score) }} match</span
                            >
                        </div>
                        <div class="px-5 py-5 sm:px-6">
                            <p
                                class="text-[15px] leading-7 whitespace-pre-line text-[#3c4c42]"
                            >
                                {{ result.answer }}
                            </p>
                            <div
                                v-if="result.citations.length"
                                class="mt-6 border-t border-[#edf0ec] pt-4"
                            >
                                <p
                                    class="mb-2 text-[11px] font-semibold tracking-[0.12em] text-[#89958d] uppercase"
                                >
                                    Sources
                                </p>
                                <div class="flex flex-wrap gap-2">
                                    <span
                                        v-for="citation in result.citations"
                                        :key="citation.title"
                                        class="inline-flex items-center gap-2 rounded-lg border border-[#e4e9e2] bg-[#fafbf9] px-3 py-2 text-xs font-medium text-[#53645a]"
                                    >
                                        <svg
                                            viewBox="0 0 16 16"
                                            fill="none"
                                            class="h-3.5 w-3.5 text-[#71917b]"
                                            aria-hidden="true"
                                        >
                                            <path
                                                d="M4 2.5h5l3 3v8H4v-11Z"
                                                stroke="currentColor"
                                                stroke-width="1.2"
                                                stroke-linejoin="round"
                                            />
                                            <path
                                                d="M9 2.5v3h3M6 8h4M6 10.5h4"
                                                stroke="currentColor"
                                                stroke-width="1.2"
                                                stroke-linecap="round"
                                            />
                                        </svg>
                                        {{ citation.title }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </article>

                    <article
                        v-else
                        class="rounded-2xl border border-[#eadfc9] bg-[#fffdf7] p-5 sm:p-6"
                    >
                        <div class="flex items-start gap-3">
                            <span
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#f7eedb] text-[#a77b2e]"
                            >
                                <svg
                                    viewBox="0 0 20 20"
                                    fill="none"
                                    class="h-5 w-5"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M10 6.5v4m0 3h.01M10 2.8l7.2 12.5H2.8L10 2.8Z"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </span>
                            <div>
                                <h2 class="text-base font-semibold">
                                    We couldn’t find this in our help docs
                                </h2>
                                <p
                                    class="mt-2 text-sm leading-6 text-[#68736b]"
                                >
                                    I don't know based on the available
                                    articles, so I won't guess. We’ve recorded
                                    this as a knowledge gap for the Harbor Desk
                                    team.
                                </p>
                                <div
                                    class="mt-4 rounded-xl border border-[#eee7d7] bg-white px-4 py-3"
                                >
                                    <p
                                        class="text-[10px] font-semibold tracking-[0.12em] text-[#9a8b6a] uppercase"
                                    >
                                        Suggested help article
                                    </p>
                                    <p
                                        class="mt-1 text-sm font-medium text-[#564d3c]"
                                    >
                                        {{ result.suggested_title }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </article>
                </section>

                <div class="mt-12 border-t border-[#e4e9e2] pt-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-semibold">
                                What this assistant can answer
                            </h2>
                            <p class="mt-1 text-xs text-[#7e8981]">
                                A small, curated set of Harbor Desk help
                                articles
                            </p>
                        </div>
                        <span
                            class="rounded-full bg-[#eaf0e9] px-2.5 py-1 text-xs font-semibold text-[#56705e]"
                            >{{ docCount }} articles</span
                        >
                    </div>
                    <p class="mt-4 text-xs leading-5 text-[#7e8981]">
                        Password and account access · Orders and changes ·
                        Shipment tracking · Delivery estimates · Returns and
                        refunds
                    </p>
                </div>
            </section>

            <aside class="lg:pt-1">
                <div class="rounded-2xl border border-[#e2e7e0] bg-white p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p
                                class="text-[11px] font-semibold tracking-[0.14em] text-[#839087] uppercase"
                            >
                                Knowledge gaps
                            </p>
                            <h2
                                class="mt-1 text-lg font-semibold tracking-tight"
                            >
                                Questions to answer next
                            </h2>
                        </div>
                        <span
                            class="flex h-9 min-w-9 items-center justify-center rounded-xl bg-[#f1f4ef] px-2 text-sm font-semibold text-[#55705e]"
                            >{{ gaps.length }}</span
                        >
                    </div>
                    <p class="mt-2 text-xs leading-5 text-[#7b877f]">
                        Unanswered questions help the team improve its help
                        center.
                    </p>

                    <div
                        v-if="gaps.length"
                        class="mt-5 divide-y divide-[#edf0ec]"
                    >
                        <article
                            v-for="gap in gaps"
                            :key="gap.id"
                            class="py-4 first:pt-0 last:pb-0"
                        >
                            <p
                                class="text-sm leading-5 font-medium text-[#39483e]"
                            >
                                {{ gap.question }}
                            </p>
                            <div
                                class="mt-2 flex items-center justify-between gap-2"
                            >
                                <span class="truncate text-xs text-[#89938c]">{{
                                    gap.suggested_title
                                }}</span>
                                <span
                                    class="shrink-0 rounded-md bg-[#f5f5f0] px-1.5 py-1 text-[10px] font-medium text-[#818477]"
                                    >{{ percent(gap.top_score) }}</span
                                >
                            </div>
                        </article>
                    </div>
                    <div
                        v-else
                        class="mt-5 rounded-xl border border-dashed border-[#dfe5de] px-4 py-6 text-center"
                    >
                        <p class="text-sm font-medium text-[#58665d]">
                            No gaps recorded yet
                        </p>
                        <p class="mt-1 text-xs leading-5 text-[#8a958e]">
                            When a question falls outside these articles, it
                            will show up here.
                        </p>
                    </div>

                    <div
                        class="mt-5 flex items-center gap-2 border-t border-[#edf0ec] pt-4 text-[11px] text-[#89938c]"
                    >
                        <span
                            class="h-1.5 w-1.5 rounded-full bg-[#d2a34b]"
                        ></span>
                        Answers require at least
                        {{ percent(threshold) }} relevance
                    </div>
                </div>

                <div class="mt-4 rounded-2xl bg-[#174b3b] p-5 text-white">
                    <p
                        class="text-xs font-semibold tracking-[0.12em] text-[#b8d4c4] uppercase"
                    >
                        Grounded by design
                    </p>
                    <p class="mt-2 text-sm leading-6 text-[#e4eee7]">
                        The assistant checks help articles first. If nothing is
                        relevant enough, it records a gap instead of making
                        something up.
                    </p>
                    <div
                        class="mt-4 flex items-center gap-2 border-t border-white/15 pt-3 text-[11px] text-[#c3d9cb]"
                    >
                        <span
                            class="h-1.5 w-1.5 rounded-full bg-[#94d8a6]"
                        ></span>
                        {{ docCount }} sources in the knowledge base
                    </div>
                </div>
            </aside>
        </main>

        <footer class="border-t border-[#e6eae4] bg-white">
            <div
                class="mx-auto flex max-w-7xl flex-col gap-1 px-5 py-5 text-xs text-[#929b94] sm:flex-row sm:items-center sm:justify-between sm:px-8"
            >
                <span>Harbor Desk help center</span>
                <span
                    >Answers grounded in the docs · Unanswered questions are
                    saved as knowledge gaps</span
                >
            </div>
        </footer>
    </div>
</template>
