<?php

namespace App\Http\Controllers\opav;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Facility;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('opav');
    }

    public function index()
    {
        $facility = Facility::select("facility.*","province.description as province")
                            ->leftJoin("province","province.id","=","facility.province")
                            ->get();
        return view('opav.home',[
            "data" => $facility
        ]);
    }
}
