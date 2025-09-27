<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function test()
    {
        return view('test', [
            'title' => 'TEST',
        ]);
    }
}
