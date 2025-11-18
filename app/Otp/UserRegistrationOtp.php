<?php

namespace App\Otp;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Hash;
use SadiqSalau\LaravelOtp\Contracts\OtpInterface as Otp;

class UserRegistrationOtp implements Otp
{
    
    public function via($notifiable)
    {
        // \Log::info("Sending OTP via channel: mail"); // Logs the channel
        return ['mail'];
    }

    /**
     * Constructs Otp class
     */
    public function __construct(
        protected string $name,
        protected string $email,
        protected string $password
    ) {
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your OTP Code')
            ->greeting("Hello {$this->name},")
            ->line('Here is your OTP for registration:')
            ->line('Your OTP: 123456') // Example OTP
            ->line('Thank you for using our application!');
    }

    /**
     * Creates the user
     */
    /**
     * Processes the Otp
     *
     * @return mixed
     */
    public function process()
    {
        /** @var User */
        $user = User::unguarded(function () {
            return User::create([
                'name'                  => $this->name,
                'email'                 => $this->email,
                'password'              => Hash::make($this->password),
                'email_verified_at'     => now(),
            ]);
        });

        event(new Registered($user));

        Auth::login($user);

        return [
            'user' => $user
        ];
    }
}
