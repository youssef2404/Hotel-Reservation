<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\MultiImage;
use App\Models\Room;
use App\Models\RoomNumber;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use PHPUnit\Framework\Constraint\Count;


class RoomController extends Controller
{
    //

    public function EditRoom($id)
    {

        $basic_facility = Facility::where('rooms_id',$id)->get();
        $multi_image = MultiImage::where('rooms_id',$id)->get();
        $editData = Room::find($id);
        $all_room_numbers = RoomNumber::where('rooms_id',$id)->get();
        return view('backend.allroom.rooms.edit_rooms',compact('editData','basic_facility','multi_image','all_room_numbers'));

    }

    public function UpdateRoom(Request $request, $id)
    {

        $room = Room::find($id);
        $room->total_adult = $request->total_adult;
        $room->total_child = $request->total_child;
        $room->room_capacity = $request->room_capacity;
        $room->discount = $request->discount;
        $room->price = $request->price;
        $room->short_desc = $request->short_desc;
        $room->description = $request->description;
        $room->view = $request->view;
        $room->bed_style = $request->bed_style;
        $room->size = $request->size;
        $room->status = 1;


        if ($request->file('image')){

            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$request->file('image')->getClientOriginalExtension();
            $image = $manager->read($request->file('image'));
            $image = $image->resize(550,850);
            $image->toJpeg(80)->save(base_path('public/upload/roomimg/'.$name_gen));
            $room['image'] = $name_gen;

            $room->save();
        }

        if($request->facility_name == null){

            $notification = array(
                'message' =>'Sorry! Not Any Basic Item Selected',
                'alert-type' => 'error'
            );

            return redirect()->back()->with($notification);
        }
        else{
            Facility::where('rooms_id',$id)->delete();
            $facilities = Count($request->facility_name);
            for($i=0; $i < $facilities; $i++)
            {
                $f_count = new Facility();
                $f_count->rooms_id = $room->id;
                $f_count->facility_name = $request->facility_name[$i];
                $f_count->save();
            }
        }

        if($room->save()){

            $files = $request->multi_img;
            if(!empty($files)){

                $subimage = MultiImage::where('rooms_id',$id)->get()->toArray();
                MultiImage::where('rooms_id',$id)->delete();

            }
            if(!empty($files)){

                foreach($files as $file){

                    $imgName = date('YmdHi').$file->getClientOriginalName();
                    $file->move('upload/roomimg/multi_img/',$imgName);
                    $subimage['multi_img'] = $imgName;

                    $subimage = new MultiImage();
                    $subimage->rooms_id = $room->id;
                    $subimage->multi_image = $imgName;
                    $subimage->save();
                }


            }


        }
        $notification = array(
            'message' =>'Room Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);

    }

    public function MultiImageDelete($id)
    {
        $delete_data = MultiImage::where('id',$id)->first();

        if($delete_data){

            $image_path = $delete_data->multi_image;
            if(file_exists($image_path)){

                unlink($image_path);
                echo "Image Unlik Successfully";
            }
            else{
                echo "Image does not  exist";

            }
        }

        $notification = array(
            'message' =>'Multi Image deleted Successfully',
            'alert-type' => 'success'
        );

        MultiImage::where('id',$id)->delete();
        return redirect()->back()->with($notification);

    }

    public function StoreRoomNumber(Request $request, $id)
    {
        $data = new RoomNumber();
        $data->rooms_id = $id;
        $data->room_type_id = $request->room_type_id;
        $data->room_number = $request->room_number;
        $data->status = $request->status;
        $data->save();

        $notification = array(
            'message' =>'Room Number Saved Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);

    }

    public function EditRoomNumber($id)
    {

        $edit_room_number = RoomNumber::find($id);
        return view('backend.allroom.rooms.edit_room_number',compact('edit_room_number'));

    }

    public function UpdateRoomNumber(Request $request, $id)
    {
        $data = RoomNumber::find($id);
        $data->room_number =  $request->room_number;
        $data->status =  $request->status;
        $data->save();

        $notification = array(
            'message' =>'Room Number Update Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('room.type.list')->with($notification);

    }

    public function DeleteRoomNumber($id)
    {

        RoomNumber::find($id)->delete();


        $notification = array(
            'message' =>'Room Number Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('room.type.list')->with($notification);


    }

    public function DeleteRoom(Request $request, $id)
    {

        $room = Room::find($id);

        if(file_exists('upload/roomimg/'.$room->image) AND ! empty($room->image)){
            @unlink('upload/roomimg/'.$room->image);
        }

        $subimage = MultiImage::where('rooms_id',$room->id)->get()->toArray();
        if(!empty($subimage)){

            foreach ($subimage as $value){
                if(!empty($value)){
                    @unlink('upload/roomimg/multi_img'.$value['multi_img']);
                }

            }
        }

        RoomType::where('id',$room->roomtype_id)->delete();
        MultiImage::where('rooms_id',$room->id)->delete();
        Facility::where('rooms_id',$room->id)->delete();
        RoomNumber::where('rooms_id',$room->id)->delete();
        $room->delete();


        $notification = array(
            'message' =>'Room Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);


    }
}
