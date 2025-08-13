<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;
use App\Http\Requests\StoreMessageRequest;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    public function index(Purchase $purchase)
    {
        // Day1はとりあえず一覧をJSONで返す or ビューに委譲でもOK
        return $purchase->messages()->with('user:id,name')->orderBy('created_at')->get();
    }

    public function store(StoreMessageRequest $request, Purchase $purchase)
    {
        $me = $request->user();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('message_images', 'public');
        }

        $purchase->messages()->create([
            'user_id'    => $me->id,
            'body'       => $request->input('body'),
            'image_path' => $imagePath,
            'read'       => false,
        ]);

        $purchase->forceFill([
            'last_message_at' => now(),
        ])->save();

        return back()->with('status', 'メッセージを送信しました。');
    }
}
