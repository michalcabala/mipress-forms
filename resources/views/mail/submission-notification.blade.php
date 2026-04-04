<x-mail::message>
# Nové odeslání formuláře: {{ $form->title }}

@php
    $labels = collect($form->fields ?? [])->mapWithKeys(fn ($field) => [($field['handle'] ?? '') => ($field['label'] ?? $field['handle'] ?? '')]);
@endphp

@foreach (($submission->data ?? []) as $key => $value)
- **{{ $labels->get($key, $key) }}:** {{ is_scalar($value) ? $value : json_encode($value) }}
@endforeach

@if ($submission->attachments->isNotEmpty())

Součástí zprávy jsou také přílohy.
@endif

</x-mail::message>
