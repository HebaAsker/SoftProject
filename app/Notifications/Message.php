<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class Message extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     * @param  array $data
     */
    public function __construct(private array $data)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [OneSignalChannel::class];
    }

  public function toOneSignal()
  {
    $message_data = $this->data['message_data'];
    return OneSignalMessage::create()
    ->setSubject($message_data['sender_name']."sent you a message.")
    ->setBody($message_data['message'])
    ->setData('data',$message_data);
  }

}
