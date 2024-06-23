<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function Index()
    {

        return view('frontend.index');

    }

    public function UserProfile()
    {
        $id = Auth::User()->id;
        $profileData = User::find($id);
        return view('frontend.dashboard.edit_profile',compact('profileData'));

    }

    public function UserProfileStore(Request $request)
    {

        $id = Auth::User()->id;
        $data = User::find($id);
        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address;

        if($request->file('photo')){

            $file = $request->file('photo');
            @unlink(public_path('upload/user_images/'.$data->photo));
            $filename = date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('upload/user_images'),$filename);
            $data['photo'] = $filename;
        }

        $data->save();

        $notification = array(
            'message' =>'User Profile Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);

    }

    public function UserLogout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $notification = array(
            'message' =>'User Logout Successfully',
            'alert-type' => 'success'
        );

        return redirect('/login')->with($notification);

    }

    public function UserChangePassword()
    {
        $id = Auth::User()->id;
        $profileData = User::find($id);
        return view('frontend.dashboard.user_change_password',compact('profileData'));

    }

    public function UserPasswordUpdate(Request $request)
    {
        $request->validate([

            'old_password' => 'required',
            'new_password' => 'required|confirmed',
        ]);

        if(!Hash::check($request->old_password, auth::User()->password)){

            $notification = array(
                'message' =>'old password does not match',
                'alert-type' => 'error'
            );

            return back()->with($notification);

        }

        User::whereId(auth::User()->id)->update([

            'password' => Hash::make($request->new_password)

        ]);
        $notification = array(
            'message' =>'password change success',
            'alert-type' => 'success'
        );

        return back()->with($notification);
    }


}
