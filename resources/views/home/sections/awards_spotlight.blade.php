@php
    $categories = \App\Models\AwardCategory::active()->forCurrentEvent()->orderBy('order')->take(3)->get();
    $content = $section->content ?? [];
@endphp

@if ($categories->isNotEmpty())
    <section class="bg-brand-50 py-20">
        <div class="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">
                {{ $content['heading'] ?? __('Freelancer Awards') }}
            </h2>
            <p class="mx-auto mt-4 max-w-2xl text-slate-600">
                {{ $content['body'] ?? __('Celebrating the freelancers, entrepreneurs and digital professionals shaping Bangladesh\'s digital economy.') }}
            </p>

            <div class="mt-10 grid gap-6 sm:grid-cols-3">
                @foreach ($categories as $category)
                    <div class="rounded-lg bg-white p-5 text-left shadow-sm">
                        <h3 class="font-bold text-slate-900">{{ $category->name }}</h3>
                        @if ($category->description)
                            <p class="mt-2 text-sm text-slate-600">{{ Str::limit($category->description, 90) }}</p>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                <a href="{{ lroute('awards.nominate') }}" class="rounded-md bg-brand-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                    {{ __('Nominate for Freelancer Awards') }}
                </a>
                <a href="{{ lroute('awards.categories') }}" class="rounded-md border border-slate-300 px-6 py-3 text-sm font-semibold text-slate-700 hover:border-brand-600 hover:text-brand-700">
                    {{ __('View Categories') }}
                </a>
            </div>
        </div>
    </section>
@endif
