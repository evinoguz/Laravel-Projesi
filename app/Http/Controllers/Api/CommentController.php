<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function create(Request $request){
        $user = Auth::user();
        if($user!=null){
            $validator = Validator::make($request->all(),[
                'post_id' => 'required',
                'comment_content' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error'=>$validator->errors()
                ], 401);
            }

            $comment = new CommentsModel();
            $comment->user_id = $user->id;
            $comment->post_id = $request->post_id;
            $comment->comment_content = $request->comment_content;

            $comment->save();
            return response()->json([
                'message'=>'comment was created',
                'user'=>$user
            ]);
        }
        return response()->json([
            'error'=>'Unauthorised'
        ], 401);
    }

    public function getComments(Request $request){
        $comments = CommentsModel::where('post_id',$request->post_id)->get();
        if($comments!=null){
            return response()->json([
                'comments'=>$comments,
                'post_id'=>$request->post_id
            ]);
        }
        return response()->json([
            'message'=>'there is no post with this id '.$request->post_id,
        ]);
    }

    public function delete(Request $request){
        $user = Auth::user();
        if($user!=null){
            $comment = CommentsModel::find($request->id);
            if($comment!=null){
                $comment->delete();
                return response()->json([
                    'message'=>'comment was deleted',
                    'user'=>$user
                ]);
            }
            return response()->json([
                'message'=>'there is no comment with id '.$request->id,
                'user'=>$user
            ]);
        }
        else{
            return response()->json([
                'error'=>'Unauthorised'
            ], 401);
        }
    }

    public function updateComment(Request $request){
        $user = Auth::user();
        if($user!=null){
            $validator = Validator::make($request->all(),[
                'post_id' => 'required',
                'comment_content' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error'=>$validator->errors()
                ], 401);
            }

            $comment = CommentsModel::find($request->id);
            if($comment!=null){
                $comment->user_id = $user->id;
                $comment->post_id = $request->post_id;
                $comment->comment_content = $request->comment_content;

                $comment->save();
                return response()->json([
                    'message'=>'comment was updated',
                    'user'=>$user
                ]);
            }
            return response()->json([
                'message'=>'you have no comment with id '.$request->id,
                'user'=>$user
            ]);
        }
        return response()->json([
            'error'=>'Unauthorised'
        ], 401);
    }
}
