<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DokoGameController extends Controller
{
    public function test()
    {
        $data = [
            'title' => 'TEST',
            'latQ' => 35.55,
            'lngQ' => 139.78,
            'delta' => 0.02,
        ];

        return view('doko_answer', compact('data'));
    }

    public function check(Request $request)
    {
        $data = [
            'title' => 'Check Answer',
            'latA' => $request->input('lat'),
            'lngA' => $request->input('lng'),
        ];

        return view('doko_check', compact('data'));
    }
}
