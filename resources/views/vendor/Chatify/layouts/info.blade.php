  @php
    $chatUser = \App\Models\User::find(request()->route('id'));
@endphp

@if ($chatUser)
    <div class="text-center mb-3">
        <div style="
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-image: url('{{ asset($chatUser->file ?? 'image/prof.jpeg') }}');
            background-size: cover;
            background-position: center;
            display: inline-block;
        "></div>
    </div>
@endif

<p class="info-name text-center">{{ $chatUser->name ?? config('chatify.name') }}</p>

<div class="messenger-infoView-btns text-center">
    <a href="#" class="danger delete-conversation">Delete Conversation</a>
</div>

{{-- shared photos --}}
<div class="messenger-infoView-shared">
    <p class="messenger-title"><span>Shared Photos</span></p>
    <div class="shared-photos-list"></div>
</div>
