<?php

namespace App\Http\Controllers\icd;
use App\Http\Controllers\Controller;
use App\Icd10;
use Illuminate\Http\Request;

class IcdController extends Controller
{

    public function index()
    {
        return "Hello World";
    }

    public function icdSearch(Request $request){
        $keyword = $request->icd_keyword;
        $icd = Icd10::where("description","like","%$keyword%")->get();
        return view("icd.icd_search",[
            "icd" => $icd
        ]);
    }
}
