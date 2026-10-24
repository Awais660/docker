<?php

namespace App\Jobs;

use App\Mail\InquiryConfirmationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;

class ProcessInquiryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $inquiry;

    /**
     * Create a new job instance.
     */
    public function __construct(array $inquiry)
    {
        $this->inquiry = $inquiry;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("ProcessInquiryJob starting for ticket #{$this->inquiry['ticket_id']}");

        // 1. Send automated confirmation email via configured mailer (Mailpit / SMTP)
        try {
            Mail::to($this->inquiry['email'])->send(new InquiryConfirmationMail($this->inquiry));
            Log::info("Email sent successfully to {$this->inquiry['email']}");
        } catch (\Throwable $e) {
            Log::error("Failed to send inquiry confirmation email: " . $e->getMessage());
        }

        // 2. Track processed inquiry in Redis for live audit
        try {
            $record = array_merge($this->inquiry, [
                'status' => 'processed',
                'processed_at' => now()->toDateTimeString(),
            ]);
            Redis::lpush('inquiries:processed_list', json_encode($record));
            Redis::ltrim('inquiries:processed_list', 0, 49); // Keep latest 50
        } catch (\Throwable $e) {
            Log::warning("Could not push to Redis inquiries list: " . $e->getMessage());
        }
    }
}
