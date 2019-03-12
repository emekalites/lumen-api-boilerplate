<?php

namespace App\Http\Controllers\V1;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function show($id)
    {
        try {
            $user = User::find($id);
            return response()->json(compact('user'), 200);
        } catch (\Exception $e){
            return response()->json(['error', 'user not found'], 200);
        }
    }

    public function update(Request $request, $id)
    {
        $validator =  Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            $error = (object) null;
            $error->validation = $validator->errors();

            return response()->json(['error'=> $error], 500);
        }

        try {
            $input = $request->all();

            $user = User::find($id);
            if($user){
                $user->name = $input['name'];
                $user->save();

                return response()->json(compact('user'), 200);
            }

            return response()->json(['error'=> 'could not update user'], 422);
        } catch (\Exception $e){
            error_log($e->getLine().' '.$e->getMessage());
            return response()->json(['error'=> 'could not update user'], 500);
        }
    }
}
