<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    //
    public function testDatabaseConnection()
    {
        $records = DB::table('books')->get();
        return response()->json(['msg' => "DB connected", $records]);
    }
}
