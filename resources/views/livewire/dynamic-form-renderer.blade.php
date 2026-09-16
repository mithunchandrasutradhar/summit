<div>
    @if ($submitted)
        <div class="rounded-md bg-green-50 p-6 text-center" role="status">
            <p class="text-lg font-semibold text-green-800">{{ __('Thank you — your submission has been received.') }}</p>
        </div>
    @else
        <form wire:submit="submit" class="grid gap-5 sm:grid-cols-2" enctype="multipart/form-data">
            <div class="absolute -left-[9999px]" aria-hidden="true">
                <label for="website">{{ __('Leave this field blank') }}</label>
                <input type="text" name="website" id="website" wire:model="website" tabindex="-1" autocomplete="off">
            </div>

            @error('submit')
                <div class="sm:col-span-2 rounded-md bg-red-50 p-3 text-sm text-red-700" role="alert" aria-live="assertive">
                    {{ $message }}
                </div>
            @enderror

            @foreach ($fields as $field)
                @php
                    $key = 'values.'.$field->field_key;
                    $describedBy = collect([
                        $field->help_text ? 'field-'.$field->field_key.'-help' : null,
                        $errors->has($key) ? 'field-'.$field->field_key.'-error' : null,
                    ])->filter()->implode(' ');
                    $ariaAttrs = 'aria-invalid="'.($errors->has($key) ? 'true' : 'false').'"'.($describedBy ? ' aria-describedby="'.$describedBy.'"' : '');
                @endphp
                <div class="{{ in_array($field->type, ['textarea', 'checkbox', 'tags']) ? 'sm:col-span-2' : '' }}">
                    @unless ($field->type === 'checkbox')
                        <label for="field-{{ $field->field_key }}" class="block text-sm font-medium text-slate-700">
                            {{ $field->label }}
                            @if ($field->is_required) <span class="text-red-500">*</span> @endif
                        </label>
                    @endunless

                    @switch($field->type)
                        @case('textarea')
                            <textarea wire:model="{{ $key }}" id="field-{{ $field->field_key }}" rows="4"
                                placeholder="{{ $field->placeholder }}" {!! $ariaAttrs !!}
                                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500"></textarea>
                            @break

                        @case('select')
                            <select wire:model="{{ $key }}" id="field-{{ $field->field_key }}" {!! $ariaAttrs !!}
                                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                <option value="">{{ __('Select...') }}</option>
                                @foreach (($field->options ?? []) as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @break

                        @case('relation_select')
                            <select wire:model="{{ $key }}" id="field-{{ $field->field_key }}" {!! $ariaAttrs !!}
                                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                <option value="">{{ __('Select...') }}</option>
                                @if ($field->relation_source && class_exists($field->relation_source))
                                    @php
                                        $relationOptions = method_exists($field->relation_source, 'selectableOptions')
                                            ? $field->relation_source::selectableOptions()
                                            : $field->relation_source::query()->get();
                                    @endphp
                                    @foreach ($relationOptions as $option)
                                        <option value="{{ $option->id }}">{{ $option->name ?? $option->title ?? $option->id }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @break

                        @case('radio')
                            <div class="mt-2 space-y-2">
                                @foreach (($field->options ?? []) as $value => $label)
                                    <label class="flex items-center gap-2 text-sm text-slate-700">
                                        <input type="radio" wire:model="{{ $key }}" value="{{ $value }}" class="border-slate-300 text-brand-600 focus:ring-brand-500">
                                        {{ $label }}
                                    </label>
                                @endforeach
                            </div>
                            @break

                        @case('checkbox')
                            <label class="flex items-start gap-2 text-sm text-slate-700">
                                <input type="checkbox" wire:model="{{ $key }}" id="field-{{ $field->field_key }}" {!! $ariaAttrs !!} class="mt-0.5 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                <span>{{ $field->label }} @if ($field->is_required) <span class="text-red-500">*</span> @endif</span>
                            </label>
                            @break

                        @case('date')
                            <input type="date" wire:model="{{ $key }}" id="field-{{ $field->field_key }}" {!! $ariaAttrs !!}
                                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                            @break

                        @case('file')
                            <input type="file" wire:model="{{ $key }}" id="field-{{ $field->field_key }}" {!! $ariaAttrs !!}
                                class="mt-1 block w-full text-sm text-slate-600">
                            @break

                        @case('tags')
                            <input type="text" wire:model="{{ $key }}" id="field-{{ $field->field_key }}"
                                placeholder="{{ $field->placeholder ?? __('Separate with commas') }}" {!! $ariaAttrs !!}
                                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                            @break

                        @case('email')
                            <input type="email" wire:model="{{ $key }}" id="field-{{ $field->field_key }}"
                                placeholder="{{ $field->placeholder }}" {!! $ariaAttrs !!}
                                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                            @break

                        @case('tel')
                            <input type="tel" wire:model="{{ $key }}" id="field-{{ $field->field_key }}"
                                placeholder="{{ $field->placeholder }}" {!! $ariaAttrs !!}
                                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                            @break

                        @case('url')
                            <input type="url" wire:model="{{ $key }}" id="field-{{ $field->field_key }}"
                                placeholder="{{ $field->placeholder }}" {!! $ariaAttrs !!}
                                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                            @break

                        @default
                            <input type="text" wire:model="{{ $key }}" id="field-{{ $field->field_key }}"
                                placeholder="{{ $field->placeholder }}" {!! $ariaAttrs !!}
                                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    @endswitch

                    @if ($field->help_text)
                        <p class="mt-1 text-xs text-slate-500" id="field-{{ $field->field_key }}-help">{{ $field->help_text }}</p>
                    @endif
                    @error($key)
                        <p class="mt-1 text-xs text-red-600" id="field-{{ $field->field_key }}-error" role="alert">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach

            <div class="sm:col-span-2">
                <button type="submit" class="rounded-md bg-brand-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                    {{ __('Submit') }}
                </button>
            </div>
        </form>
    @endif
</div>
