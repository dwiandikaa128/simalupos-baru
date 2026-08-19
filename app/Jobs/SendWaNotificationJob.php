<?php

namespace App\Jobs;

use App\Models\CustomerMutation;
use App\Services\WhatsAppNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWaNotificationJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries = 2;

    public function __construct(public CustomerMutation $mutation)
    {
    }

    public function handle(WhatsAppNotificationService $waService): void
    {
        $waService->sendMutationNotification($this->mutation);
    }
}
