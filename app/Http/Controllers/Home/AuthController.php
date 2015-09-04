<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    /**
     * All controllers in Home directory have to be authorised, with middleware 'auth'.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
}
