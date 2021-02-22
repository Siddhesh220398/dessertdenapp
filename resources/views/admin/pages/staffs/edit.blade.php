@extends('admin.layouts.app')



@section('breadcrumb')

    {!! Breadcrumbs::render('edit_staffs', $staff) !!}

@endsection



@section('content')

    <style>
        td {

            height: 70px;
            width: 100px;
        }

    </style>

    <div class="row ">

        <div class="col-md-12">

            <!-- BEGIN SAMPLE FORM PORTLET-->

            <div class="portlet light bordered">

                <div class="portlet-title">

                    <div class="caption">

                        <i class="fa {{ $icon }} font-green"></i>

                        <span class="caption-subject font-green bold uppercase">{{ $title }}</span>

                    </div>

                </div>

                <div class="portlet-body">

                    <form id="frmStaffs" class="form-horizontal" role="form" method="POST"
                          action="{{ route('admin.staffs.update', $staff->id) }}" enctype="multipart/form-data">

                        @csrf

                        @method('PUT')


                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">

                            <label for="name" class="col-md-2 control-label">{!! $mend_sign !!} Name</label>

                            <div class="col-md-6">

                                <div class="input-icon">

                                    <i class="fa fa-map-marker"></i>

                                    <input type="text" class="form-control" name="name" id="name"
                                           placeholder="Enter franchise name" maxlength="80"
                                           value="{{ old('name' , $staff->name) }}">

                                    @if ($errors->has('name'))

                                        <span class="help-block">

                                    <strong>{{ $errors->first('name') }}</strong>

                                </span>

                                    @endif

                                </div>

                            </div>

                        </div>


                        <div class="form-group">

                            <label for="user_type" class="col-md-2 control-label">{!! $mend_sign !!}Type</label>

                            <div class="col-md-6">

                                <select class="form-control" name="user_type">

                                    <option value="">Select Type</option>
                                    <option value="Admin" @if($staff->type == 'Admin') selected @endif >Admin</option>

                                    <option value="Chef" @if($staff->type == 'Chef') selected @endif >Chef</option>

                                    <option value="Deliveryboy" @if($staff->type == 'Deliverboy') selected @endif>
                                        Delivery Boy
                                    </option>


                                </select>

                                @if ($errors->has('user_type'))

                                    <span class="help-block">

                                <strong>{{ $errors->first('user_type') }}</strong>

                            </span>

                                @endif

                            </div>

                        </div>

                        <div class="form-group">
                            <label for="type" class="col-md-2 control-label">{!! $mend_sign !!}Department Type</label>
                            <div class="col-md-6">

                                <select class="mdb-select form-control" name="category_id" id="category_id">

                                    <option value="">Select Category</option>

                                    <option value="0" @if($staff->category_id =='0') selected @endif> Cake</option>
                                    <option value="1" @if($staff->category_id =='1') selected @endif>Bakery</option>
                                    <option value="2" @if($staff->category_id =='2') selected @endif>Others</option>


                                </select>

                            </div>

                        </div>


                        <div class="form-group{{ $errors->has('profile') ? ' has-error' : '' }}">

                            <label for="name" class="col-md-2 control-label">Profile</label>

                            <div class="col-md-6">

                                <div class="input-icon">

                                    <i class="fa fa-upload"></i>

                                    <input type="file" class="form-control" name="profile" id="profile"
                                           accept=".jpg,.jpeg,.png">

                                    @if ($errors->has('profile'))

                                        <span class="help-block">

                                    <strong>{{ $errors->first('profile') }}</strong>

                                </span>

                                    @endif

                                </div>

                            </div>

                        </div>


                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">

                            <label for="email" class="col-md-2 control-label">{!! $mend_sign !!}Email</label>

                            <div class="col-md-6">

                                <div class="input-icon">

                                    <i class="fa fa-map-marker"></i>

                                    <input type="text" class="form-control" name="email" placeholder="Enter Email"
                                           maxlength="80" value="{{ old('email', $staff->email) }}">

                                    @if ($errors->has('email'))

                                        <span class="help-block">

                                    <strong>{{ $errors->first('email') }}</strong>

                                </span>

                                    @endif

                                </div>

                            </div>

                        </div>


                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">

                            <label for="password" class="col-md-2 control-label">{!! $mend_sign !!} Password</label>

                            <div class="col-md-6">

                                <div class="input-icon">

                                    <i class="fa fa-map-marker"></i>

                                    <input type="text" class="form-control" name="password" id="password"
                                           placeholder="Enter Password" maxlength="80"
                                           value="{{ old('password' , $staff->view_password) }}">

                                    @if ($errors->has('password'))

                                        <span class="help-block">

                                    <strong>{{ $errors->first('password') }}</strong>

                                </span>

                                    @endif

                                </div>

                            </div>

                        </div>

                        <div class="form-group{{ $errors->has('view_password') ? ' has-error' : '' }}">

                            <label for="view_password" class="col-md-2 control-label">{!! $mend_sign !!} Show
                                Password</label>

                            <div class="col-md-6">

                                <div class="input-icon">

                                    <i class="fa fa-map-marker"></i>

                                    <input type="text" class="form-control" name="view_password" id="view_password"
                                           placeholder="Enter Password" maxlength="80"
                                           value="{{ old('view_password' , $staff->view_password) }} " disabled>

                                    @if ($errors->has('view_password'))

                                        <span class="help-block">

                                    <strong>{{ $errors->first('view_password') }}</strong>

                                </span>

                                    @endif

                                </div>

                            </div>

                        </div>


                        <div class="form-group{{ $errors->has('mobile_no') ? ' has-error' : '' }}">

                            <label for="mobile" class="col-md-2 control-label">{!! $mend_sign !!}Mobile No</label>

                            <div class="col-md-6">

                                <div class="input-icon">

                                    <i class="fa fa-map-marker"></i>

                                    <input type="text" class="form-control" name="mobile" placeholder="Enter MobileNo"
                                           value="{{ old('mobile', $staff->mobile) }}">

                                    @if ($errors->has('mobile'))

                                        <span class="help-block">

                                    <strong>{{ $errors->first('mobile') }}</strong>

                                </span>

                                    @endif

                                </div>

                            </div>

                        </div>

                        <table class="col-12" border="1"
                               style="width:80%;height: 100%; margin-left: 100px;  text-align: center;">
                            <thead>
                            <td>Sr.No</td>
                            <td>Sections</td>
                            <td>Access</td>
                            <td>Add</td>
                            <td>Edit</td>
                            <td>View</td>
                            <td>Delete</td>
                            <td>Import</td>
                            </thead>
                            <tbody>
                            @foreach($sections as $section)
                                <tr>

                                    <td>{{$section->id}}</td>
                                    <td>{{$section->name}}</td>
                                    <td><input type="checkbox" id="access"
                                               name="section[{{$section->id}}][permissions][]"
                                               @if(isset($section_array[$section->id] )&& in_array('access',$section_array[$section->id]['permissions']))) checked @endif value="access">
                                    </td>
                                    <td><input type="checkbox" id="add" name="section[{{$section->id}}][permissions][]"
                                               value="add"  @if(isset($section_array[$section->id] )&& in_array('add',$section_array[$section->id]['permissions']))) checked @endif></td>
                                    <td><input type="checkbox" id="edit" name="section[{{$section->id}}][permissions][]"
                                               value="edit"  @if(isset($section_array[$section->id] )&& in_array('edit',$section_array[$section->id]['permissions']))) checked @endif></td>
                                    <td><input type="checkbox" id="view" name="section[{{$section->id}}][permissions][]"
                                               value="view"  @if(isset($section_array[$section->id] )&& in_array('view',$section_array[$section->id]['permissions']))) checked @endif></td>
                                    <td><input type="checkbox" id="delete"
                                               name="section[{{$section->id}}][permissions][]" value="delete"  @if(isset($section_array[$section->id] )&& in_array('delete',$section_array[$section->id]['permissions']))) checked @endif></td>
                                    <td><input type="checkbox" id="import"
                                               name="section[{{$section->id}}][permissions][]" value="import"  @if(isset($section_array[$section->id] )&& in_array('import',$section_array[$section->id]['permissions']))) checked @endif></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <br/>

                        <div class="form-group">

                            <div class="col-md-offset-2 col-md-10">

                                <button type="submit" class="btn green">Submit</button>

                                <a href="{{route('admin.staffs.index')}}" class="btn red">Cancel</a>

                            </div>

                        </div>


                    </form>

                </div>

            </div>

            <!-- End: SAMPLE FORM PORTLET -->

        </div>

    </div>

@endsection



@push('scripts')

    <script type="text/javascript">


        $(document).ready(function () {

            $("#frmStaffs").validate({

                rules: {

                    name: {

                        required: true,

                        maxlength: 80,

                        not_empty: true,

                    },

                    mobile: {

                        required: true,

                    },

                    email: {

                        required: true,

                        email: true

                    },


                },

                messages: {

                    name: {

                        required: "@lang('validation.required',['attribute'=>'name'])",

                        maxlength: "@lang('validation.max.string',['attribute'=>'name','max'=>80])",

                        not_empty: "@lang('validation.not_empty',['attribute'=>'name'])",

                    },

                    mobile: {

                        required: "@lang('validation.required',['attribute'=>'mobile'])",

                    },

                    email: {

                        required: "@lang('validation.required',['attribute'=>'email'])",

                    },


                },

                errorClass: 'help-block',

                errorElement: 'span',

                highlight: function (element) {

                    $(element).closest('.form-group').addClass('has-error');

                },

                unhighlight: function (element) {

                    $(element).closest('.form-group').removeClass('has-error');

                },

                errorPlacement: function (error, element) {

                    if (element.attr("data-error-container")) {

                        error.appendTo(element.attr("data-error-container"));

                    } else {

                        error.insertAfter(element);

                    }

                }

            });


            $("#frmStaffs").submit(function () {

                if ($(this).valid()) {

                    addOverlay();

                    return true;

                } else {

                    return false;

                }

            });

        });


    </script>

@endpush
