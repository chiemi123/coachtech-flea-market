<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Message;

class MessageReceived extends Notification
{
    protected $message;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('新しいメッセージが届きました')
            ->greeting($notifiable->name . ' さん')
            ->line('新しいメッセージが届いています。')
            ->line('内容：' . $this->message->body)
            ->action('チャット画面を開く', route('purchases.chat', $this->message->purchase_id))
            ->line('ご確認ください。');
    }
}
