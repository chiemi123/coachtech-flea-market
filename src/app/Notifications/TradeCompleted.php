<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Purchase;

class TradeCompleted extends Notification
{
    protected $purchase;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Purchase $purchase)
    {
        $this->purchase = $purchase;
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
            ->subject('取引が完了しました')
            ->greeting($notifiable->name . ' さん')
            ->line('購入者が商品に対して取引完了の操作を行いました。')
            ->action('取引詳細を確認する', url("/purchases/{$this->purchase->id}"))
            ->line('ご確認の上、評価をお願いいたします。');
    }
}
