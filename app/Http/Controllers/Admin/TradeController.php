<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Programme;
use App\Models\Trade;
use Illuminate\Http\Request;

class TradeController extends Controller
{
    public function index()
    {
        $trades = Trade::with('programme.department')->withCount('courses')->orderBy('trade_name')->paginate(15);
        $programmes = Programme::with('department')->orderBy('name')->get();
        return view('admin.trades.index', compact('trades', 'programmes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'trade_name' => ['required', 'string', 'max:255'],
            'programme_id' => ['nullable', 'exists:programmes,id'],
        ]);
        Trade::create($data);
        return back()->with('status', 'Trade added.');
    }

    public function update(Request $request, Trade $trade)
    {
        $data = $request->validate([
            'trade_name' => ['required', 'string', 'max:255'],
            'programme_id' => ['nullable', 'exists:programmes,id'],
        ]);
        $trade->update($data);
        return back()->with('status', 'Trade updated.');
    }

    public function destroy(Trade $trade)
    {
        $trade->delete();
        return back()->with('status', 'Trade deleted.');
    }
}
