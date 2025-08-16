<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRatingRequest;
use App\Models\Rating;
use App\Models\Purchase;
use Illuminate\Database\QueryException;

class RatingController extends Controller
{
    public function store(StoreRatingRequest $request, Purchase $purchase)
    {
        $me = auth()->user();

        abort_unless($purchase->status === 'completed', 422);

        $data = $request->validated();

        try {
            Rating::create([
                'purchase_id' => $purchase->id,
                'rater_id'    => $me->id,
                'ratee_id'    => $data['ratee_id'],
                'score'       => $data['score'],
            ]);
        } catch (QueryException $e) {
            // ユニーク制約（purchase_id, rater_id）の衝突をハンドリング
            return back()->withErrors(['rating' => 'すでに評価済みです。'])->withInput();
        }

        // 要件(FN014)：評価送信後は「商品一覧」へ遷移
        // ルート名はプロジェクトに合わせて変更
        return redirect()->route('items.index')
            ->with('success', '評価を送信しました。ありがとうございます。');
    }
}
