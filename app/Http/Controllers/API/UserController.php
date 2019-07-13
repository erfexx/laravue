<?php

namespace App\Http\Controllers\API;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use \Intervention\Image\Facades\Image;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    public function index()
    {
        //
        return User::latest()->paginate(5);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'      =>  'required|string|max:150',
            'email'     =>  'required|string|email|unique:users|max:150',
            'password'  =>  'required|string|min:6',
        ]);

        return User::create([
            'name'      =>  $request['name'],
            'email'     =>  $request['email'],
            'password'  =>  Hash::make($request['password']),
            'bio'       =>  $request['bio'],
            'photo'     =>  $request['photo'],
            'type'      =>  $request['type'],
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    public function profile()
    {
        return auth('api')->user();
    }

    public function updateProfile(Request $request)
    {
        $user = auth('api')->user();
        $currPhoto = $user->photo;

        $this->validate($request, [
            'name'      =>  'required|string|max:150',
            'email'     =>  'required|string|email|max:100|unique:users,email,'.$user->id,
            'password'  =>  'sometimes|required|min:6',
        ]);

        if($request->photo != $currPhoto){
            $name = time().'.'.
                explode('/',
                    explode(':',
                        substr(
                            $request->photo,
                            0,
                            strpos($request->photo,';')
                        ))[1])[1];

            Image::make($request->photo)->save(public_path('img/profile/').$name);

            $request->merge(['photo' => $name]);

            $userPhoto = public_path('img/profile/').$currPhoto;
            if(file_exists($userPhoto)){
                @unlink($userPhoto);

            }
        }

        if (!empty($request->password)){
            $request->merge(['password' => Hash::make($request['password'])]);
        }

        $user->update($request->all());
        return ['message' => 'update succeed'];
    }
    
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $user = User::findOrFail($id);

        $this->validate($request, [
            'name'      =>  'required|string|max:150',
            'email'     =>  'required|string|email|unique:users,email,'.$user->id,
            'password'  =>  'sometimes|min:6',
        ]);

        $user->update($request->all());

        return ['message' => 'user update'.$id];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $user = User::findOrFail($id);

        $user->delete();

        return ['message' => 'user deleted'];
    }
}
