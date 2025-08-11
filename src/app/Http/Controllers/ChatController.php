<?php

namespace App\Http\Controllers;

use App\Models\Purchase;

class ChatController extends Controller
{
    public function show(Purchase $purchase)
    {
        $purchase->load(['item', 'messages.user']);
        $messages = $purchase->messages()->orderBy('created_at')->get();

        return view('chat.show', compact('purchase', 'messages'));
    }
}
