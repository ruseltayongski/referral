<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FeedbackCtrl extends Controller
{
    public function home(){
        return view('feedback.feedback');
    }

    public function CommentAppend(Request $request){
        $textarea_body = view('feedback.textarea_body');
        $comment_append = view('feedback.comment_append',[
            "comment" => $request->comment
        ]);
        $view = $textarea_body.'explode|ruseltayong|explode'.$comment_append;
        return $view;
    }
}
