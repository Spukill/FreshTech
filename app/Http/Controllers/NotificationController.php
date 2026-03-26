<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Events\OrderNotification;
use App\Events\StockNotification;

class NotificationController extends Controller
{
    // Lista todas as notificações do usuário
    public function index()
    {
        $buyer = Auth::user()->buyer;
        
        if (!$buyer) {
            return redirect()->route('home')->with('error', 'Buyer profile not found.');
        }

        $notifications = Notification::where('id_buyer', $buyer->id)
            ->orderBy('date_not', 'desc')
            ->paginate(15);

        return view('pages.notifications', compact('notifications'));
    }

    // Retorna notificações não lidas (para o badge)
    public function unreadCount()
    {
        $buyer = Auth::user()->buyer;
        
        if (!$buyer) {
            return response()->json(['count' => 0]);
        }

        $count = Notification::where('id_buyer', $buyer->id)
            ->unread()
            ->count();

        return response()->json(['count' => $count]);
    }

    // Marca uma notificação como lida
    public function markAsRead($id)
    {
        $buyer = Auth::user()->buyer;
        
        $notification = Notification::where('id', $id)
            ->where('id_buyer', $buyer->id)
            ->firstOrFail();

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    // Marca todas como lidas
    public function markAllAsRead()
    {
        $buyer = Auth::user()->buyer;
        
        Notification::where('id_buyer', $buyer->id)
            ->unread()
            ->update(['viewed' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }

    // Cria notificação de mudança de status de order
    public static function createOrderStatusNotification($orderId, $buyerId, $newStatus)
    {
        \Log::info('Creating notification', ['order_id' => $orderId, 'buyer_id' => $buyerId, 'status' => $newStatus]);
        
        $statusMessages = [
            'in distribution' => 'Your order #' . $orderId . ' is now in distribution!',
            'delivered' => 'Your order #' . $orderId . ' has been delivered!',
            'cancelled' => 'Your order #' . $orderId . ' has been cancelled.',
        ];

        $title = $statusMessages[$newStatus] ?? 'Order status updated';

        $notification = Notification::create([
            'id_buyer' => $buyerId,
            'title' => $title,
            'date_not' => now(),
            'viewed' => false,
        ]);
        
        \Log::info('Notification created', ['notification_id' => $notification->id]);
        
        // Broadcast the notification in real-time
        broadcast(new OrderNotification($notification, $buyerId));
        
        return $notification;
    }

    // Cria notificação de produto disponível
    public static function createProductAvailableNotification($productId, $productName, $buyerId)
    {
        $notification = Notification::create([
            'id_buyer' => $buyerId,
            'title' => 'Good news! "' . $productName . '" is back in stock!',
            'date_not' => now(),
            'viewed' => false,
        ]);
        
        // Broadcast the notification in real-time
        broadcast(new StockNotification($notification, $buyerId));
        
        return $notification;
    }

    // Cria notificação de produto sem stock
    public static function createProductOutOfStockNotification($productId, $productName, $buyerId)
    {
        $notification = Notification::create([
            'id_buyer' => $buyerId,
            'title' => '"' . $productName . '" is now out of stock.',
            'date_not' => now(),
            'viewed' => false,
        ]);
        
        // Broadcast the notification in real-time
        broadcast(new StockNotification($notification, $buyerId));
        
        return $notification;
    }
}
