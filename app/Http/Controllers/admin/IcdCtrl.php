<?php

namespace App\Http\Controllers\admin;

use App\Icd10;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class IcdCtrl extends Controller
{
    public function view(){
        $icd = Icd10::OrderBy('code', 'asc')->paginate(20);
        return view('admin.icd.icd',[
                "icd" => $icd
        ]);
    }

    public function search(Request $request){
        if(isset($request->keyword)){
            $keyword = $request->keyword;
            $icd = Icd10::where('description', 'like', '%'.$keyword.'%')
                        ->orWhere('code', 'like', '%'.$keyword.'%')->OrderBy('code', 'asc')
                        ->paginate(20)->appends(["keyword" => $keyword]);
        }else{
            $icd = Icd10::OrderBy('code', 'asc')->paginate(20);   
        }
        return view('admin.icd.icd',[
                "icd" => $icd
        ]);
    }
}
