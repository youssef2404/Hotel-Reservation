<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class BlogController extends Controller
 {
    //

    public function BlogCategory()
    {

        $category = BlogCategory::latest()->get();
        return view('backend.category.blog_category',compact('category'));

    }

    public function StoreBlogCategory(Request $request)
    {

        BlogCategory::insert([

            'category_name' => $request->category_name,
            'category_slug' => strtolower(str_replace(' ','-',$request->category_name)),
        ]);

        $notification = array(
            'message' =>'BlogCategory Added Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    public function EditBlogCategory($id)
    {
        $categories = BlogCategory::find($id);
        return response()->json($categories);

    }

    public function UpdateBlogCategory(Request $request)
    {
        $cat_id = $request->cat_id;

        $categories = BlogCategory::find($cat_id);
            $categories->update([
                'category_name' => $request->category_name,
                'category_slug' => strtolower(str_replace(' ', '-', $request->category_name)),
            ]);

        $notification = array(
            'message' =>'BlogCategory Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);


    }

    public function DeleteBlogCategory($id)
    {
        $category = BlogCategory::find($id);
        $category->delete();

        $notification = array(
            'message' =>'BlogCategory Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);

    }

    public function AllBlogPost()
    {
        $post = BlogPost::latest()->get();
        return view('backend.post.all_post',compact('post'));

    }

    public function AddBlogPost()
    {
        $blogcat = BlogCategory::latest()->get();
        return view('backend.post.add_post',compact('blogcat'));

    }

    public function StoreBlogPost(Request $request)
    {

        if($request->file('post_image')){

            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$request->file('post_image')->getClientOriginalExtension();
            $image = $manager->read($request->file('post_image'));
            $image = $image->resize(550,370);
            $image->toJpeg(80)->save(base_path('public/upload/post/'.$name_gen));
            $save_url = 'upload/post/'.$name_gen;

            BlogPost::insert([

                'blogcat_id' => $request->blogcat_id,
                'user_id' => Auth::user()->id,
                'post_title' => $request->post_title,
                'post_slug' => strtolower(str_replace(' ', '-', $request->category_name)),
                'short_descp' => $request->short_descp,
                'long_descp' => $request->long_descp,
                'post_image' => $save_url,
                'created_at' => Carbon::now(),

            ]);

            $notification = array(
                'message' =>'Blog Post Data Inserted Successfully',
                'alert-type' => 'success'
            );

            return redirect()->route('all.blog.post')->with($notification);

        }
        else{

            $notification = array(
                'message' =>'You should Insert Image',
                'alert-type' => 'error'
            );

            return redirect()->back()->with($notification);


        }

    }

    public function EditBlogPost($id)
    {
        $blogcat = BlogCategory::latest()->get();
        $post = BlogPost::find($id);
        return view('backend.post.edit_post', compact('post','blogcat'));

    }

    public function UpdateBlogPost(Request $request)
    {

        $post_id = $request->post_id;

        if ($request->file('post_image')) {

            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()) . '.' . $request->file('post_image')->getClientOriginalExtension();
            $image = $manager->read($request->file('post_image'));
            $image = $image->resize(550, 370);
            $image->toJpeg(80)->save(base_path('public/upload/post/' . $name_gen));
            $save_url = 'upload/post/' . $name_gen;


            BlogPost::findOrFail($post_id)->update([

                'blogcat_id' => $request->blogcat_id,
                'user_id' => Auth::user()->id,
                'post_title' => $request->post_title,
                'post_slug' => strtolower(str_replace(' ', '-', $request->category_name)),
                'short_descp' => $request->short_descp,
                'long_descp' => $request->long_descp,
                'post_image' => $save_url,
                'created_at' => Carbon::now(),

            ]);

            $notification = array(
                'message' => 'Blog Post Updated With Image Successfully',
                'alert-type' => 'success'
            );

            return redirect()->route('all.blog.post')->with($notification);

        } else {


            BlogPost::findOrFail($post_id)->update([

                'blogcat_id' => $request->blogcat_id,
                'user_id' => Auth::user()->id,
                'post_title' => $request->post_title,
                'post_slug' => strtolower(str_replace(' ', '-', $request->category_name)),
                'short_descp' => $request->short_descp,
                'long_descp' => $request->long_descp,
                'created_at' => Carbon::now(),

            ]);


            $notification = array(
                'message' => 'Blog Post Updated Without Image Successfully',
                'alert-type' => 'success'
            );


            return redirect()->route('all.blog.post')->with($notification);

        }
    }


    public function DeleteBlogPost($id)
    {
        $item = BlogPost::findOrFail($id);
        $img = $item->post_image;
        unlink($img);

        BlogPost::findOrFail($id)->delete();

        $notification = array(
            'message' =>'Blog Post Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);

    }

    public function BlogDetails($slug)
    {
        $blog = BlogPost::where('post_slug',$slug)->first();
        $bcategory = BlogCategory::latest()->get();
        $lpost = BlogPost::latest()->limit(3)->get();

        return view('frontend.blog.blog_details',compact('blog','bcategory','lpost'));
    }

    public function BlogCatList($id)
    {
        $blog = BlogPost::where('blogcat_id',$id)->get();
        $bcategory = BlogCategory::latest()->get();
        $lpost = BlogPost::latest()->limit(3)->get();

        return view('frontend.blog.blog_cat_list',compact('blog','bcategory','lpost'));



    }
}
