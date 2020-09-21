<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class UserController extends Controller
{
  public function main(Request $request){
    // return $request->userid;
    $userdata = DB::table('users')->where('id',$request->userid)->get();
    // $lastword = substr($userdata,1,-1);
    $alluserdata = DB::table('post')->get();
    // $postlist = json_encode(DB데이터 들어가야함,JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);
    $jsondata = json_encode($alluserdata,JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);//jsondata 쉽게 만들기

    return $jsondata;

  }

}
