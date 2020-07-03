<?php

namespace App\Http\Controllers\Eoc;

use App\Inventory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class InventoryController extends Controller
{
    public function Inventory($facility_id){
        $inventory = Inventory::where("facility_id",$facility_id)->first();
        if(!$inventory){
            $inventory = new Inventory();
            $inventory->facility_id = $facility_id;
            $inventory->name = "Regular Covid Beds";
            $inventory->capacity = 0;
            $inventory->occupied = 0;
            $inventory->available = 0;
            $inventory->status = 'Active';
            $inventory->save();

            $inventory = new Inventory();
            $inventory->facility_id = $facility_id;
            $inventory->name = "ICU Beds";
            $inventory->capacity = 0;
            $inventory->occupied = 0;
            $inventory->available = 0;
            $inventory->status = 'Active';
            $inventory->save();

            $inventory = new Inventory();
            $inventory->facility_id = $facility_id;
            $inventory->name = "Mechanical Ventilators";
            $inventory->capacity = 0;
            $inventory->occupied = 0;
            $inventory->available = 0;
            $inventory->status = 'Active';
            $inventory->save();

            $inventory = new Inventory();
            $inventory->facility_id = $facility_id;
            $inventory->name = "Dialysis Machines";
            $inventory->capacity = 0;
            $inventory->occupied = 0;
            $inventory->available = 0;
            $inventory->status = 'Active';
            $inventory->save();

            $inventory = new Inventory();
            $inventory->facility_id = $facility_id;
            $inventory->name = "Patients Waiting for Admission";
            $inventory->capacity = 0;
            $inventory->occupied = 0;
            $inventory->available = 0;
            $inventory->status = 'Active';
            $inventory->save();
        }

        $inventory = Inventory::where("facility_id",$facility_id)->get();

        return view('eoc.inventory',[
            "inventory" => $inventory,
            "facility_id" => $facility_id
        ]);
    }
}
