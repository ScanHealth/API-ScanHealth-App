<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rules;
use App\Models\User;
use App\Models\Images;
use Faker\Provider\Image;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;



class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken("myapptoken")->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function uploadImg(Request $request) {

        $request->validate([ 
            'user_id' => 'required',
            'file'  => 'required|mimes:png,jpg|max:2048',
        ]);   

 
        if ($files = $request->file('file')) {
             
            //store file into document folder
            $file = $request->file->store('public/documents');
 
            //store your file into database
            $image = new Images();
            $image->title = $file;
            $image->user_id = $request->user_id;
            $image->save();

            $response = [
                "success" => true,
                "message" => "File successfully uploaded",
                "file" => $file
            ];
  
            return response($response, 201);

        }
    }

}
