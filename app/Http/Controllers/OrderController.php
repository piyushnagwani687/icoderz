<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrder;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with('user')->paginate(10);
        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        return view('orders.create', ['users' => $users]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(
            [
            'user_id' => 'required|exists:users,id',
            'product_names.*' => 'required|string',
            'quantities.*' => 'required|integer|min:1',
            'amounts.*' => 'required|numeric|min:0.01',
            ],
            [
                'user_id' => 'The user field is required',
                'product_names.*' => 'The product name field is required',
                'quantities.*' => 'The quantity field is required',
                'amounts.*' => 'The amount field is required'
            ]
        );

        $order = new Order();
        $order->user_id = $request->user_id;
        $order->grand_total = $request->grand_total;
        $order->save();

        foreach($request->product_names as $key => $product_name)
        {
            $product = new Product();
            $product->order_id = $order->id;
            $product->product_name = $product_name;
            $product->quantity = $request->quantities[$key];
            $product->amount = $request->amounts[$key];
            $product->total = $request->totals[$key];
            $product->save();
        }

        return response()->json([
            'status' => 'success',
            'redirect' => route('orders.index')
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $order = Order::with('products')->findOrFail($id);
        return view('orders.show', compact('order'));
    }
}
