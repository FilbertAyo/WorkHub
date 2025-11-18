<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div class='mb-3'>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" />
            <x-text-input id="update_password_current_password" name="current_password" type="password"
                class="form-control mt-1 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div class='mb-3'>
            <x-input-label for="update_password_password" :value="__('New Password')" />
            <x-text-input id="update_password_password" name="password" type="password"
                class="form-control mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div class='mb-3'>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password"
                class="form-control mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>


    <header class="my-4">
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Upload your signature') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __('Ensure the image is clear and readable. Accepted formats: jpeg, png, jpg, gif. Max size: 2MB.') }}
        </p>
    </header>

    @if (Auth::user()->signature)
        <div class="mb-4">
            <img src="{{ asset(Auth::user()->signature) }}" alt="Signature" class="h-12 border rounded" style="height: 200px; width: auto;">
        </div>
    @endif

    <form action="{{ route('profile.image') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="mb-3">
            <x-input-label for="signature" :value="__('Signature')" />
            <x-text-input id="signature" name="signature" type="file" class="form-control mt-1 block w-full" />
            <x-input-error :messages="$errors->get('signature')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button label="Upload"/>

            @if (session('status') === 'signature-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-success">{{ __('Signature updated successfully.') }}</p>
            @endif
        </div>
    </form>


</section>
