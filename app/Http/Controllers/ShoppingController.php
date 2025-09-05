<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShoppingController extends Controller
{
    //

    public function index(Request $request)
    {
        $name = $request->input('name');
        $data = $request->all();
        dump($data);

        return view('hello', [
            'name' => $name
        ]);
    }
}
