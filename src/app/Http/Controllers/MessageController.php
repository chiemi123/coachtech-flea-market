<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Purchase $purchase)
    {
        // Day1はとりあえず一覧をJSONで返す or ビューに委譲でもOK
        return $purchase->messages()->with('user:id,name')->orderBy('created_at')->get();
    }

    public function store(Request $request, Purchase $purchase)
    {
        // Day3でFormRequest＋バリデーション実装予定。いったんダミー。
        return back()->with('status', 'Day3で実装します');
    }
}
