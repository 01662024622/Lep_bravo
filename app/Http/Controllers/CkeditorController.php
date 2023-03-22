<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CkeditorController extends Controller
{
    public function upload(Request $request)
    {
        if ($request->hasFile('upload')) {
            $originName = $request->file('upload')->getClientOriginalName();
            $fileName= pathinfo($originName, PATHINFO_FILENAME);
            $extension= $request->file('upload')->getClientOriginalExtension();
            $fileName=$fileName.'_'.time().'.'.$extension;

            $request->file('upload')->move(public_path('images'),$fileName);
            $CKEditorFuncNum=$request->input('CKEditorFuncNum');
            $url='/images.'.$fileName;
            $msg= 'succes';
            $response="<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum,'$url','$msg')</script>";
            @header('Content-type: text/html; chasert=utf8');
            return $response;
        }
        return "";
    }
}
