<?php

namespace App\Http\Controllers\Opcen;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OpcenController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function Opcen(){
        return view('opcen.opcen');
    }

    public function newClient(){
        return view('opcen.new_client');
    }

    public function bedAvailable(){
        return view('opcen.bed_available');
    }

}
