<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MailController extends Controller
{
    public function sendEmail(){
        $details=[
            'title'=>'Mail From book selling software [powerd by THÚY] @2020_phu',
            'body'=>'Bạn vui lòng truy cập vào link sao _____ mã xác thực của bạn là 293802'
        ];
        Mail::to('phule9225@gmail.com')->send(new TestMail($details));
        return "Email Sent";
    }
}
