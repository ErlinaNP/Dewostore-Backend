<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartProduct;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Product::paginate();
        return response()->json($data,200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function cart(Request $request,$id)
    {
        // try {
            $user = $request->get('user');
            $product = Product::findOrFail($id);
            if($product){
                $cart = Cart::where('user_id',$user->id)->where('seller_id',$product->seller_id)->first();
                if(!empty($cart)){
                    $cartProduct =  new CartProduct;
                    $cartProduct->cart_id = $cart->id;
                    $cartProduct->product_id = $product->id;
                    $cartProduct->save();
                    return response()->json($cartProduct);
                }else{
                    $newCart = new Cart;
                    $newCart->user_id = $user->id;
                    $newCart->seller_id = $product->seller_id;
                    $newCart->save();
                    $cartProduct =  new CartProduct;
                    $cartProduct->cart_id = $newCart->id;
                    $cartProduct->product_id = $product->id;
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
        $cart = Cart::with('')->where('user_id',$user->id)->get();
        return response()->json($cart);
    }

    public function deleteCart($id)
    {
        CartProduct::where('cart_id',$id)->delete();
        Cart::where('id',$id)->delete();
        return response()->json('success');
    }
}
