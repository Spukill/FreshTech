<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Buyer;
use App\Models\Order;
use App\Http\Controllers\NotificationController;
use App\Events\ProfileUpdated;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ProfileController extends Controller
{
    public function showProfile(Request $request): View
    {
        $user = Auth::user();
        $buyer = $user->buyer;
        Gate::authorize('viewProfile', $buyer);
    
        $editMode = $request->query('edit', 'false');

        if ($buyer->shoppingCart !== NULL) {
            $orders = $buyer->shoppingCart->pluck('orders')->filter()->values();
        } else $orders = NULL;

        $isOauth = !empty($user->google_id);

        // Render the 'pages.profile' view with the needed vars
        return view('pages.profile', [
            'user' => $user,
            'edit' => $editMode,
            'orders' => $orders,
            'isOauth' => $isOauth,
        ]);
    }

    public function showOrder(Request $request): View|RedirectResponse
    {   
        $user = Auth::user();
        $buyer = $user->buyer;
        
        $orderId = $request->route('order');
        try {
            $order = Order::findOrFail($orderId);
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Order not found.');
        }

        Gate::authorize('view', $order);

        return view('pages.order', [ // for the purchase history
            'user' => $user,
            'order' => $order,
        ]);
    }

    public function cancelOrder(Request $request)
    {   
        $user = Auth::user();
        $buyer = $user->buyer;
        
        $orderId = $request->route('order');
        try {
            $order = Order::findOrFail($orderId);
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Order not found.');
        }

        Gate::authorize('cancel', $order);

        $order->status = "cancelled";
        $order->save();

        // Create notification for order cancellation
        NotificationController::createOrderStatusNotification(
            $order->id,
            $buyer->id,
            'cancelled'
        );

        return redirect()->route('profile')
            ->withSuccess('You have cancelled the order successfully!');
    }

    public function editProfile(Request $request)
    {
        $user = Auth::user();
        $buyer = $user->buyer;
        Gate::authorize('editProfile', $buyer);
        // Only allow changing email and password for non-OAuth users
        $isOauth = !empty($user->google_id);

        // Validation rules (password can't be less than 8 for example)
        $rules = [];
        if ($request->filled('user_name')) {
            $rules['user_name'] = 'string|max:255';
        }
        if ($request->filled('password') && !$isOauth) {
            $rules['password'] = 'string|min:8|confirmed';
        }

        if (!empty($rules)) {
            $request->validate($rules);
        }

        if ($request->filled('user_name')) {
            $buyer->user_name = $request->input('user_name');
        }
        if ($request->filled('password') && !$isOauth) {
            // Hash the password before saving so the user can log in with it.
            $user->password = Hash::make($request->input('password'));
        }

        $buyer->save();
        $user->save();

        if ($request->wantsJson()) {
            return response()->json($buyer);
        }

        broadcast(new ProfileUpdated($buyer->id));
        return redirect()->route('profile');
    }


}
