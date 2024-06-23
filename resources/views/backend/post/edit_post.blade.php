@extends('admin.admin_dashboard')
@section('admin')


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>


    <div class="page-content">
        <!--breadcrumb-->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">Edit Blog Post</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Blog Post</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!--end breadcrumb-->
        <div class="container">
            <div class="main-body">
                <div class="row">
                    <div class="col-lg-12">
                        <form class="row g-3" action="{{ route('update.blog.post')}}" method="post" enctype="multipart/form-data" >
                            @csrf

                            <input type="hidden" id="id" name="post_id" value="{{$post->id}}">
                            <input type="hidden" id="id" name="category_name" value="{{$post['blog']['category_name']}}">

                            <div class="col-md-6">
                                <label for="input7" class="form-label">Blog Category</label>
                                <select id="input7" class="form-select" name="blogcat_id">
                                    <option selected="">Select Category</option>
                                    @foreach($blogcat as $category)
                                        <option value="{{$category->id}}" {{$category->id == $post->blogcat_id ? 'selected' : ''}}>
                                            {{$category->category_name}}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="col-md-6">
                                <label for="input1" class="form-label">Post Title</label>
                                <input type="text" name="post_title" class="form-control" id="input1" value="{{$post->post_title}}">
                            </div>

                            <div class="col-md-12">
                                <label for="input11" class="form-label">Short Description</label>
                                <textarea class="form-control" id="input11" name="short_descp" rows="3">{{$post->short_descp}}</textarea>
                            </div>


                            <div class="col-md-12">
                                <label for="input11" class="form-label">Post Description</label>
                                <textarea class="form-control" id="input11" name="long_descp" rows="3">{!!$post->long_descp !!}</textarea>
                            </div>



                            <div class="col-md-6">
                                <label for="input1" class="form-label">Photo</label>
                                <input type="file" name="post_image" class="form-control" id="image"  />
                            </div>


                            <div class="col-md-6">
                                <img id="showImage" src="{{asset($post->post_image)}}"
                                     alt="Admin" class="rounded-circle p-1 bg-primary" width="80">
                            </div>


                            <div class="col-md-12">
                                <div class="d-md-flex d-grid align-items-center gap-3">
                                    <button type="submit" class="btn btn-primary px-4">Submit</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script type="text/javascript">

        $(document).ready(function () {
            $('#image').change(function (e) {

                let reader = new FileReader();
                reader.onload = function (e) {
                    $('#showImage').attr('src',e.target.result);

                }
                reader.readAsDataURL(e.target.files['0']);

            })

        })

    </script>


    <script type="text/javascript">
        $(document).ready(function (){
            $('#myForm').validate({
                rules: {
                    name: {
                        required : true,
                    },
                    position: {
                        required : true,
                    },
                    facebook: {
                        required : true,
                    },

                },
                messages :{
                    name: {
                        required : 'Please Enter Team Name',
                    },
                    position: {
                        required : 'Please Enter Team position',
                    },
                    facebook: {
                        required : 'Please Enter Team facebook',
                    },


                },
                errorElement : 'span',
                errorPlacement: function (error,element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight : function(element, errorClass, validClass){
                    $(element).addClass('is-invalid');
                },
                unhighlight : function(element, errorClass, validClass){
                    $(element).removeClass('is-invalid');
                },
            });
        });

    </script>






@endsection
