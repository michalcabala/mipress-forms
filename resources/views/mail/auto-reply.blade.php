<x-mail::message>
# {{ $form->auto_reply_subject ?: 'Děkujeme za zprávu' }}

{{ $form->auto_reply_body ?: 'Děkujeme, ozveme se vám co nejdříve.' }}

</x-mail::message>
