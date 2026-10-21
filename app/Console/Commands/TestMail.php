<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMail extends Command
{
    protected $signature = 'mail:test {email=test@example.com}';

    protected $description = 'Send a test email through Mailtrap';

    public function handle(): int
    {
        Mail::raw('Your Laravel Mailtrap configuration is working!', function ($message): void {
            $message
                ->to($this->argument('email'))
                ->subject('Laravel Mailtrap Test');
        });

        $this->info('Test email sent successfully.');

        return self::SUCCESS;
    }
}
