<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\Testimonial;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class TestimonialController extends Controller
{
    //

    public function AllTestimonial()
    {

        $testimonial = Testimonial::latest()->get();
        return view('backend.testimonial.all_testimonial',compact('testimonial'));


    }

    public function AddTestimonial()
    {

        return view('backend.testimonial.add_testimonial');

    }


    public function StoreTestimonial(Request $request)
    {

        if($request->file('image')){

            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$request->file('image')->getClientOriginalExtension();
            $image = $manager->read($request->file('image'));
            $image = $image->resize(50,50);
            $image->toJpeg(80)->save(base_path('public/upload/testimonial/'.$name_gen));
            $save_url = 'upload/testimonial/'.$name_gen;

            Testimonial::insert([

                'name' => $request->name,
                'city' => $request->city,
                'message' => $request->message,
                'image' => $save_url,
            ]);

            $notification = array(
                'message' =>'Testimonial Data Inserted Successfully',
                'alert-type' => 'success'
            );

            return redirect()->route('all.testimonial')->with($notification);

        }
        else{

            $notification = array(
                'message' =>'You should Insert Image',
                'alert-type' => 'error'
            );

            return redirect()->back()->with($notification);


        }


    }

    public function EditTestimonial($id)
    {
        $testimonial = Testimonial::find($id);
        return view('backend.testimonial.edit_testimonial', compact('testimonial'));

    }

    public function UpdateTestimonial(Request $request)
    {

        $testimonial_id = $request->id;

        if ($request->file('image')){

            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$request->file('image')->getClientOriginalExtension();
            $image = $manager->read($request->file('image'));
            $image = $image->resize(50,50);
            $image->toJpeg(80)->save(base_path('public/upload/testimonial/'.$name_gen));
            $save_url = 'upload/testimonial/'.$name_gen;

            Testimonial::findOrFail($testimonial_id)->update([

                'name' => $request->name,
                'city' => $request->city,
                'message' => $request->message,
                'image' => $save_url,
                'created_at' => Carbon::now(),
            ]);

            $notification = array(
                'message' =>'Testimonial Updated With Image Successfully',
                'alert-type' => 'success'
            );

            return redirect()->route('all.testimonial')->with($notification);


        }
        else{

            Testimonial::findOrFail($testimonial_id)->update([

                'name' => $request->name,
                'city' => $request->city,
                'message' => $request->message,
                'created_at' => Carbon::now(),

            ]);

            $notification = array(
                'message' =>'Testimonial Updated Without Image Successfully',
                'alert-type' => 'success'
            );

            return redirect()->route('all.testimonial')->with($notification);

        }

    }

    public function DeleteTestimonial($id)
    {
        Testimonial::findOrFail($id)->delete();

        $notification = array(
            'message' =>'testimonial Image Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);

    }
}
