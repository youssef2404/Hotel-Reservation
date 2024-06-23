<?php

namespace App\Http\Controllers;

use App\Models\BookArea;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class BookAreaController extends Controller
{
    public function BookArea()
    {

        $book = BookArea::find(1);
        return view('backend.bookarea.book_area',compact('book'));

    }

    public function BookAreaUpdate(Request $request)
    {
        $book_id = $request->id;

        if ($request->file('image')){

            @unlink(public_path('public/upload/book_area/'.$request->image));
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$request->file('image')->getClientOriginalExtension();
            $image = $manager->read($request->file('image'));
            $image = $image->resize(600,600);
            $image->toJpeg(80)->save(base_path('public/upload/book_area/'.$name_gen));
            $save_url = 'upload/book_area/'.$name_gen;


            BookArea::findOrFail($book_id)->update([

                'short_title' => $request->short_title,
                'main_title' => $request->main_title,
                'short_desc' => $request->short_desc,
                'link_url' => $request->link_url,
                'image' => $save_url,
                'created_at' => Carbon::now(),

            ]);

            $notification = array(
                'message' =>'Book Area Update With Image Successfully',
                'alert-type' => 'success'
            );

            return redirect()->back()->with($notification);


        }
        else{

            BookArea::findOrFail($book_id)->update([

                'short_title' => $request->short_title,
                'main_title' => $request->main_title,
                'short_desc' => $request->short_desc,
                'link_url' => $request->link_url,
                'created_at' => Carbon::now(),

            ]);

            $notification = array(
                'message' =>'Book Area Update Without Image Successfully',
                'alert-type' => 'success'
            );

            return redirect()->back()->with($notification);

        }

    }
}
