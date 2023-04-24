<?php

namespace App\Jobs;

use App\Helpers\NotificationHelper;
use App\Models\Order;
use App\Models\Settings;
use App\Models\User;
use App\Traits\Loggable;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};

class AttachDeliveryMan implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Loggable;

    public ?Order $order;
    public ?string $language;

    /**
     * Create a new event instance.
     *
     * @param Order|null $order
     * @param string|null $language
     */
    public function __construct(?Order $order, ?string $language)
    {
        $this->order    = $order;
        $this->language = $language;
    }


    /**
     * Handle the event
     * @return void
     */
    public function handle(): void
    {
        $result = [];

        try {
            $order = $this->order;

            $second = Settings::adminSettings()->where('key', 'deliveryman_order_acceptance_time')->first();

            if (empty($order) || $order->delivery_type !== Order::DELIVERY) {
                return;
            }

            $items = [];

            $users = User::with('deliveryManSetting')
                ->whereHas('deliveryManSetting', fn(Builder $query) => $query->where('online', 1)
                    ->where(function ($q) {
                        $q->where('updated_at', '>=', date('Y-m-d H:i', strtotime('-15 minutes')))
                            ->orWhere('created_at', '>=', date('Y-m-d H:i', strtotime('-15 minutes')));
                    })

                )
                ->where('firebase_token', '!=', null)
                ->select(['firebase_token', 'id'])
                ->get();

            $serverKey = Settings::adminSettings()->where('key', 'server_key')->pluck('value')->first();

            $headers = [
                'Authorization' => "key=$serverKey",
                'Content-Type'  => 'application/json'
            ];

            foreach ($users as $user) {
                $items[] = [
                    'firebase_token'    => $user->firebase_token,
                    'user'              =>  $user,
                ];

            }

            foreach (collect($items)->whereNotNull('firebase_token')->sort(SORT_ASC) as $item) {

                $deliveryMan = data_get(Order::select(['id', 'deliveryman'])->find($order->id), 'deliveryman');

                if (!empty($deliveryMan)) {
                    continue;
                }

                $data = [
                    'registration_ids'  => is_array(data_get($item, 'firebase_token')) ? data_get($item, 'firebase_token') : [data_get($item, 'firebase_token')],
                    'notification'      => [
                        'title'         => "New order #$order->id",
                        'body'          => 'need attach deliveryman',
                    ],
                    'data'              => (new NotificationHelper)->deliveryManOrder($order, $this->language)
                ];

                $result[] = Http::withHeaders($headers)->post('https://fcm.googleapis.com/fcm/send', $data)->json();

                sleep(data_get($second, 'value', 30));
            }

        } catch (Exception $e) {
            Log::error($e->getMessage(), [$e->getCode(), $e->getLine()]);
//            $this->error($e);
        }

        Log::error('$result', [$result]);
    }
}
