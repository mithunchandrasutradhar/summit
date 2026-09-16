@php $content = $section->content ?? []; @endphp

<section class="bg-brand-700">
    <div class="mx-auto max-w-4xl px-4 py-16 text-center sm:px-6 lg:px-8">
        <h2 class="text-2xl font-extrabold tracking-tight text-white sm:text-3xl">
            {{ $content['heading'] ?? __('Be part of the national freelancing movement.') }}
        </h2>
        <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
            <a href="{{ lroute('register.index') }}" class="rounded-md bg-white px-6 py-3 text-sm font-semibold text-brand-700 shadow-sm hover:bg-brand-50">
                {{ __('Register for Summit') }}
            </a>
            <a href="#" class="rounded-md border border-white/40 px-6 py-3 text-sm font-semibold text-white hover:border-white">
                {{ __('Become a Sponsor') }}
            </a>
        </div>
    </div>
</section>
