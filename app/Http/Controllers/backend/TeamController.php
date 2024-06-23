<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


class TeamController extends Controller
{
    //

    public function AllTeam()
    {

        $team = Team::latest()->get();
        return view('backend.team.all_team', compact('team'));


    }

    public function AddTeam()
    {

        return view('backend.team.add_team');


    }

    public function TeamStore(Request $request)
    {
        if($request->file('image')){

            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$request->file('image')->getClientOriginalExtension();
            $image = $manager->read($request->file('image'));
            $image = $image->resize(370,246);
            $image->toJpeg(80)->save(base_path('public/upload/team/'.$name_gen));
            $save_url = 'upload/team/'.$name_gen;

            Team::insert([

                'name' => $request->name,
                'position' => $request->position,
                'facebook' => $request->facebook,
                'image' => $save_url,
                'created_at' => Carbon::now(),

            ]);

            $notification = array(
                'message' =>'Team Data Inserted Successfully',
                'alert-type' => 'success'
            );

            return redirect()->route('all.team')->with($notification);

        }
        else{

            $notification = array(
                'message' =>'You should Insert Image',
                'alert-type' => 'error'
            );

            return redirect()->back()->with($notification);


        }

    }

    public function EditTeam($id)
    {
        $team = Team::findOrFail($id);
        return view('backend.team.edit_team',compact('team'));

    }

    public function TeamUpdate(Request $request)
    {

        $team_id = $request->id;

        if ($request->file('image')){

            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$request->file('image')->getClientOriginalExtension();
            $image = $manager->read($request->file('image'));
            $image = $image->resize(370,246);
            $image->toJpeg(80)->save(base_path('public/upload/team/'.$name_gen));
            $save_url = 'upload/team/'.$name_gen;

            Team::findOrFail($team_id)->update([

                'name' => $request->name,
                'position' => $request->position,
                'facebook' => $request->facebook,
                'image' => $save_url,
                'created_at' => Carbon::now(),

            ]);

            $notification = array(
                'message' =>'Team Update With Image Successfully',
                'alert-type' => 'success'
            );

            return redirect()->route('all.team')->with($notification);


        }
        else{

            Team::findOrFail($team_id)->update([

                'name' => $request->name,
                'position' => $request->position,
                'facebook' => $request->facebook,
                'created_at' => Carbon::now(),

            ]);

            $notification = array(
                'message' =>'Team Update Without Image Successfully',
                'alert-type' => 'success'
            );

            return redirect()->route('all.team')->with($notification);

        }

    }

    public function DeleteTeam($id)
    {

        Team::findOrFail($id)->delete();

        $notification = array(
            'message' =>'Team Image Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);


    }
}
