<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    use Queueable;

    private $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Notification channels: mail + database
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Email content
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Order Received')
            ->greeting('Hello '.$notifiable->name.'!')
            ->line('You have received a new order from '.$this->order->customer_name.'.')
            ->line('Total Price: '.number_format($this->order->total_price, 2))
            ->action('View Order', url('/orders/'.$this->order->id))
            ->line('Thank you for using Digital Menu App!');
    }

    /**
     * Database notification content
     */
    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'customer_name' => $this->order->customer_name,
            'total_price' => $this->order->total_price,
            'message' => 'New order received from '.$this->order->customer_name,
        ];
    }
}
