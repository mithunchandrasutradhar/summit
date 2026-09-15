@php $content = $section->content ?? []; @endphp

<section class="mx-auto max-w-4xl px-4 py-20 text-center sm:px-6 lg:px-8">
    <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">
        {{ $content['heading'] ?? __('What is Freelancer Summit Bangladesh 2026?') }}
    </h2>
    <p class="mt-6 text-lg text-slate-600">
        {{ $content['body'] ?? __('Freelancer Summit Bangladesh 2026 is the largest national platform dedicated to freelancers, aspiring freelancers, digital professionals, AI practitioners, entrepreneurs, students, marketplaces, technology companies and the wider digital economy ecosystem of Bangladesh — building momentum nationwide before culminating in the Grand Summit in Dhaka.') }}
    </p>
</section>
