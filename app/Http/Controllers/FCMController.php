<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use App\Models\User;
use FCM;
use DB;

class FCMController extends Controller
{
    //
    static public function fcm($title, $body, $token, $key, $num){
        $tokens = array();

        if(sizeof($token) > 0 ){
            foreach ($token as $Rresult) {
                $tokens[] = $Rresult->Token;
            }
        }

        $myMessage = "새글이 등록되었습니다.";

        $message = array(
          'title' => $title,
          'body' => $body,
          'key' => $key,
          'num' => $num
        );

        $url = 'https://fcm.googleapis.com/fcm/send';
        $fields = array(
                    'registration_ids' => $tokens,
                    'data' => $message
                );

        $headers = array(
                'Authorization:key =' . 'AAAA9066qgY:APA91bE6OlNYEv2zXEIH7pfPwMElEKBZxK81i99QJ30gKu47Vt9Hc6jvDQxhD_RnSXD0V4AEkPyV_FkBUEMFahAuvUpgCpxzA9EoolMhgOaOf7FEHOKOJzRq3DF3QrNcSXkf8QnVsEdI'
                ,'Content-Type:application/json'
                );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        // return $fields;
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);

        echo $result;
    }

    public function alsetting(Request $request){

      $id = $request->userid;
      $onoff = $request->onoff;
      $key = $request->key;

      if( $key == "k"){
        DB::table('users')->where('id',$id)->update(['pushkeyword' => $onoff]);
      }
      elseif ($key == "b") {
        DB::table('users')->where('id',$id)->update(['pushboard' => $onoff]);
      }
      elseif ($key == "c") {
        DB::table('users')->where('id',$id)->update(['pushcomment' => $onoff]);
      }
      return $request;
    }

    public function keywordadd(Request $request){
      $id = $request->userid;
      $text = $request->keyword;
      $ck = DB::table('keyword')->where([
        'userid' => $id,
        'text' =>$text
      ])->get()->count();

      if($ck<1){
        DB::table('keyword')->insert([
          'userid' => $id,
          'text' => $text
        ]);
      }
      return $ck;
    }

    public function getkeyword(Request $request){
      $id =  $request->userid;
      $data = DB::table('keyword')
      ->join('users', 'users.id', '=', 'keyword.userid')
      ->where('userid',$id)->get();
      $push = DB::table('users')->where('id',$id)->get();
      return json_encode(compact("data", "push"),JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);
    }

    public function removekeyword(Request $request){
      $id = $request->userid;
      $text = $request->keyword;

      DB::table('keyword')->where([
        'userid' => $id,
        'text' => $text
      ])->delete();

      return $request;
    }

    public function chart(Request $request){
      $id1 = $request->id1;
      $id2 = $request->id2;
      $me = $request->me;
      broadcast(new \App\Events\chartEvent($id1, $id2, $me));
    }
}
