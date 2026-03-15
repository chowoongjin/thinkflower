<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PhotoListController extends Controller
{
    public function index(Request $request)
    {
        $productName = trim((string) $request->input('product_name', ''));

        $query = DB::table('order_photos as op')
            ->join('orders as o', 'op.order_id', '=', 'o.id')
            ->select([
                'op.id',
                'op.file_path',
                'op.created_at',
                'op.photo_type',
                'op.sort_order',
                'o.order_no',
                'o.product_name',
                'o.product_detail',
            ]);

        if ($productName !== '') {
            if ($productName === '근조화환') {
                $query->where(function ($q) {
                    $q->where('o.product_name', 'like', '근조3단%')
                        ->orWhere('o.product_name', 'like', '근조화환%');
                });
            } elseif ($productName === '축하화환') {
                $query->where(function ($q) {
                    $q->where('o.product_name', 'like', '축하3단%')
                        ->orWhere('o.product_name', 'like', '축하화환%');
                });
            } else {
                $query->where('o.product_name', 'like', '%' . $productName . '%');
            }
        }

        $photos = $query
            ->orderByDesc('op.created_at')
            ->orderByDesc('op.id')
            ->paginate(18)
            ->withQueryString();

        $data = compact('photos', 'productName');

        if ($request->ajax()) {
            return view('pages.partials.photo-list-content', $data);
        }

        return view('pages.photo-list', $data);
    }
}
