<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartProduct;
use App\Models\PhotoProduct;
use App\Models\Order;
use App\Models\OrderDetail;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title = '';
        $category = '';
        if ($request->input('title')) {
            $title = $request->input('title');
        }
        if ($request->input('category')) {
            $category = $request->input('category');
            if ($request->input('seller_id')) {
                $data = Product::where('seller_id', $request->input('seller_id'))->where('title', 'LIKE', '%' . $title . '%')->where('category_id', '=', $category)->paginate();
            } else {
                $data = Product::where('title', 'LIKE', '%' . $title . '%')->where('category_id', '=', $category)->paginate();
            }
        } else {
            if ($request->input('seller_id')) {
                $data = Product::where('seller_id', $request->input('seller_id'))->where('title', 'LIKE', '%' . $title . '%')->paginate();
            } else {
                $data = Product::where('title', 'LIKE', '%' . $title . '%')->paginate();
            }
        }
        return response()->json($data, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $photoPath = array();
        $path = '';
        if ($request->hasfile('photo')) {
            $ic = 0;
            foreach ($request->file('photo') as $file) {
                if ($ic == 0) {
                    $path = $file->store('files/products');
                } else {
                    $tempPath = $file->store('files/products');
                    array_push($photoPath, $tempPath);
                }
                $ic++;
            }
        }
        $user = $request->get('user');
        $data = new Product();
        $data->title = $request->input('title');
        $data->photo = $path;
        $data->description = $request->input('description');
        $data->original_price = $request->input('original_price');
        $data->weight = $request->input('weight');
        $data->sku = $request->input('sku');
        $data->in_stock = $request->input('stock');
        $data->category_id = $request->input('category_id');
        $data->seller_id = $user->id;
        $data->save();
        foreach ($photoPath as $value) {
            $dataP = new PhotoProduct();
            $dataP->id_product = $data->id;
            $dataP->link = $value;
            $dataP->save();
        }
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Product::find($id);
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $photoPath = array();
        $path = '';
        if ($request->hasfile('photo')) {
            $ic = 0;
            foreach ($request->file('photo') as $file) {
                if ($ic == 0) {
                    $path = $file->store('files/products');
                } else {
                    $tempPath = $file->store('files/products');
                    array_push($photoPath, $tempPath);
                }
                $ic++;
            }
        }
        $user = $request->get('user');
        $data = Product::find($id);
        if ($request->input('title')) {

            $data->title = $request->input('title');
        }
        if (strlen($path) > 0) {

            $data->photo = $path;
        }
        if ($request->input('description')) {

            $data->description = $request->input('description');
        }
        if ($request->input('original_price')) {

            $data->original_price = $request->input('original_price');
        }
        if ($request->input('weight')) {

            $data->weight = $request->input('weight');
        }
        if ($request->input('sku')) {
            $data->sku = $request->input('sku');
        }
        if ($request->input('sku')) {
            $data->in_stock = $request->input('stock');
        }
        if ($request->input('category_id')) {

            $data->category_id = $request->input('category_id');
        }
        $data->save();
        foreach ($photoPath as $value) {
            $dataP = new PhotoProduct();
            $dataP->id_product = $data->id;
            $dataP->link = $value;
            $dataP->save();
        }
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Product::findOrFail($id)->delete();
        return response()->json("berhasil");
    }
    public function order(Request $request)
    {
        $user = $request->get('user');
        $order = Order::with('orderDetail')->where('buyer_id', $user->id)->orderBy('created_at', 'DESC')->get();
        return response()->json($order);
    }
    public function orderSeller(Request $request)
    {
        $user = $request->get('user');
        $order = Order::with('orderDetail')->where('seller_id', $user->id)->orderBy('created_at', 'DESC')->get();
        return response()->json($order);
    }
    public function cart(Request $request, $id)
    {
        // try {
        $user = $request->get('user');
        $product = Product::findOrFail($id);
        if (!empty($product)) {
            $cart = Cart::where('user_id', $user->id)->where('seller_id', $product->seller_id)->first();
            if (!empty($cart)) {
                $cartProductCheck = CartProduct::where('cart_id', $cart->id)->where('product_id', $product->id)->first();
                if (!empty($cartProductCheck)) {
                    $cp = $cartProductCheck;
                    $cp->jumlah = $cp->jumlah + $request->input('jumlah');
                    if ($request->input('catatan')) {
                        $cp->catatan = $request->input('catatan');
                    }
                    $cp->save();
                    return response()->json($cp);
                } else {
                    $cartProduct =  new CartProduct;
                    $cartProduct->cart_id = $cart->id;
                    $cartProduct->product_id = $product->id;
                    if ($request->input('catatan')) {
                        $cartProduct->catatan = $request->input('catatan');
                    }
                    $cartProduct->jumlah = $request->input('jumlah');
                    $cartProduct->save();
                    return response()->json($cartProduct);
                }
            } else {
                $newCart = new Cart;
                $newCart->user_id = $user->id;
                $newCart->seller_id = $product->seller_id;
                $newCart->save();
                $cartProduct =  new CartProduct;
                $cartProduct->cart_id = $newCart->id;
                $cartProduct->product_id = $product->id;
                if ($request->input('catatan')) {
                    $cartProduct->catatan = $request->input('catatan');
                }
                $cartProduct->jumlah = $request->input('jumlah');
                $cartProduct->save();
                return response()->json($newCart);
            }
        }
        // } catch (\Throwable $th) {
        //     return response()->json($th);
        // }
    }

    public function getCart(Request $request)
    {
        $user = $request->get('user');
        $cart = Cart::with('cartProduct.product')->where('user_id', $user->id)->get();
        return response()->json($cart);
    }
    public function orderbyid(Request $request, $id)
    {
        $order = Order::with('orderDetail.product')->find($id);
        return response()->json($order);
    }

    public function deleteCart($id)
    {
        CartProduct::where('id', $id)->delete();
        return response()->json('success');
    }
    function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    public function checkoutCart(Request $request)
    {
        $user = $request->get('user');
        $cartData = array();
        $order = new Order();
        $order->invoice = ProductController::generateRandomString();
        $order->alamat = $request->input('alamat');
        $order->seller_id = $request->input('seller_id');
        if ($request->input('kurir') == "20000") {
            $order->kurir = "Sicepat";
            $order->ongkir = $request->input("kurir");
        } elseif ($request->input('kurir') == "23000") {
            $order->kurir = "J&T";
            $order->ongkir = $request->input("kurir");
        } else {
            $order->kurir = "JNE";
            $order->ongkir = $request->input("kurir");
        }
        $order->status = "UNPAID";
        $order->buyer_id = $user->id;
        $total = 0;
        $arrP = array();
        foreach ($request->input('cart') as $key => $value) {
            $cp = CartProduct::with('product')->find($value);
            $cp->jumlah = $request->input('cartSum')[$key];
            $total += $cp->product->original_price * $cp->jumlah;
            array_push($arrP, $cp);
        }
        $order->price = $total;

        \Midtrans\Config::$serverKey = 'SB-Mid-server-8gP0RWfsr-BkNR5MRCwa1Ocy'; // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized = true;
        $params = array(
            'transaction_details' => array(
                'order_id' => $order->invoice,
                'gross_amount' => $total + $order->ongkir,
            ),
            'customer_details' => array(
                'first_name' => $user->name,
                'last_name' => $user->name,
                'email' => $user->email,
            ),
        );
        $snapToken = \Midtrans\Snap::getSnapToken($params);
        $order->token = $snapToken;
        $order->save();
        foreach ($arrP as $value) {
            $op = new OrderDetail();
            $op->order_id = $order->id;
            $op->title = $value->product->title;
            $op->product_id = $value->product->id;
            $op->sum = $value->jumlah;
            $op->price = $value->product->original_price * $op->price;
            $op->save();
        }
        return response()->json($order, 200);
    }
    public function checkout(Request $request)
    {
        $user = $request->get('user');
        $cartData = array();
        $order = new Order();
        $order->invoice = ProductController::generateRandomString();
        $order->alamat = $request->input('alamat');
        foreach ($request->input('cart') as $value) {
            $cp = CartProduct::with('product')->find($value);
            array_push($cartData, $cp);
        };
        return response()->json($order, 400);
        // $user = $request->get('user');
        // $cart = Cart::with('cartProduct.product')->where('user_id', $user->id)->get();
        // $sumPrice = 0;


        // $snapToken = \Midtrans\Snap::getSnapToken($params);
        // return response()->json($snapToken);
    }
    public function payment(Request $request)
    {
        if($request->input('order_id')){
            $order = Order::where('invoice', $request->input('order_id'))->first();
            if(!$order){
                $order = Order::where('invoice', substr($request->input('order_id'), 0, -3))->first();
            }
            $order->status = $request->input('transaction_status');
            $order->save();
            return response()->json($order);
        }else{
            $order = Order::where('invoice', $request->input('transaction_id'))->first();
            $order->status = $request->input('transaction_status');
            $order->save();
            return response()->json($order);
        }
    }
}
