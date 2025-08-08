<?php

namespace App\Jobs;

use App\Models\Email;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Class SendRegisterNotification
 * @package App\Jobs
 *
 * Send the notification of register to user
 */
class SendRegisterNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User $user
     */
    private $user;
    /**
     * @var string $password
     */
    private $password;

    /**
     * SendRegisterNotification constructor.
     *
     * @param User $user
     * @param string $password
     */
    public function __construct(User $user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    public function handle()
    {
        Email::sendRegistrationNotification($this->user, $this->password);
    }
}
