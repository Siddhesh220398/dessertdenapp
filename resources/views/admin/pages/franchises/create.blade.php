@extends('admin.layouts.app')



@section('breadcrumb')

    {!! Breadcrumbs::render('add_franchises') !!}

@endsection



@section('content')

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

                    <form id="frmFranchise" class="form-horizontal" role="form" method="POST"
                          action="{{ route('admin.franchises.store') }}" enctype="multipart/form-data">

                        @csrf


                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">

                            <label for="name" class="col-md-2 control-label">{!! $mend_sign !!}Franchise Name</label>

                            <div class="col-md-6">

                                <div class="input-icon">

                                    <i class="fa fa-map-marker"></i>

                                    <input type="text" class="form-control" name="name" id="name"
                                           placeholder="Enter franchise name" maxlength="80" value="{{ old('name') }}">

                                    @if ($errors->has('name'))

                                        <span class="help-block">

                                        <strong>{{ $errors->first('name') }}</strong>

                                    </span>

                                    @endif

                                </div>

                            </div>

                        </div>


                        <div class="form-group">

                            <label for="city" class="col-md-2 control-label">{!! $mend_sign !!}City</label>

                            <div class="col-md-6">

                                <select class="form-control city_id" id="city_id" name="city_id" id="city_id">

                                    <option value="">Select City</option>

                                    @foreach($cities as $city)

                                        <option value="{{$city->id}}"
                                                @if(old('city_id') == $city->id) selected @endif>{{$city->name}}</option>

                                    @endforeach

                                </select>

                            </div>

                        </div>


                        <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">

                            <label for="address" class="col-md-2 control-label">{!! $mend_sign !!}Address</label>

                            <div class="col-md-6">


                                <textarea class="form-control rounded-0" id="address" rows="3" name="address"
                                          value="{{old('address')}}"></textarea>

                                @if ($errors->has('address'))

                                    <span class="help-block">

                                        <strong>{{ $errors->first('address') }}</strong>

                                    </span>

                                @endif

                            </div>

                        </div>


                        <div class="form-group{{ $errors->has('mobile_no') ? ' has-error' : '' }}">

                            <label for="mobile_no" class="col-md-2 control-label">{!! $mend_sign !!}Mobile No</label>

                            <div class="col-md-6">

                                <div class="input-icon">

                                    <i class="fa fa-map-marker"></i>

                                    <input type="text" class="form-control" name="mobile_no" id="mobile_no"
                                           placeholder="Enter MobileNo" value="{{ old('mobile_no') }}">

                                    @if ($errors->has('mobile_no'))

                                        <span class="help-block">

                                        <strong>{{ $errors->first('mobile_no') }}</strong>

                                    </span>

                                    @endif

                                </div>

                            </div>

                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">

                            <label for="password" class="col-md-2 control-label">{!! $mend_sign !!}Password</label>

                            <div class="col-md-6">

                                <div class="input-icon">

                                    <i class="fa fa-map-marker"></i>

                                    <input type="text" class="form-control" name="password" id="password"
                                           placeholder="Enter password" value="{{ old('password') }}">

                                    @if ($errors->has('password'))

                                        <span class="help-block">

                                        <strong>{{ $errors->first('password') }}</strong>

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

                                    <input type="text" class="form-control" name="email" id="email"
                                           placeholder="Enter Email" value="{{ old('email') }}">

                                    @if ($errors->has('email'))

                                        <span class="help-block">

                                        <strong>{{ $errors->first('email') }}</strong>

                                    </span>

                                    @endif

                                </div>

                            </div>

                        </div>

                        <div class="form-group">
                            <label for="is_visible" class="col-md-2 control-label">{!! $mend_sign !!} Visible</label>
                            <div class="col-md-6">

                                <select class="mdb-select form-control" name="is_visible" id="is_visible">
                                    <option value="yes"> Yes</option>
                                    <option value="no">No</option>
                                </select>

                            </div>

                        </div>

                        <div class="form-group{{ $errors->has('balance') ? ' has-error' : '' }}">

                            <label for="balance" class="col-md-2 control-label">{!! $mend_sign !!}Balance</label>

                            <div class="col-md-6">

                                <div class="input-icon">

                                    <i class="fa fa-map-marker"></i>

                                    <input type="text" class="form-control" name="balance" id="balance"
                                           placeholder="Enter Balance" maxlength="80" value="{{ old('balance') }}">

                                    @if ($errors->has('balance'))

                                        <span class="help-block">

                                        <strong>{{ $errors->first('balance') }}</strong>

                                    </span>

                                    @endif

                                </div>

                            </div>

                        </div>


                        <div class="form-group{{ $errors->has('gstn_no') ? ' has-error' : '' }}">

                            <label for="gstn_no" class="col-md-2 control-label">GST NO</label>

                            <div class="col-md-6">

                                <div class="input-icon">

                                    <i class="fa fa-map-marker"></i>

                                    <input type="text" class="form-control" name="gstn_no" id="gstn_no"
                                           placeholder="Enter GST No" maxlength="80" value="{{ old('gstn_no') }}">

                                    @if ($errors->has('gstn_no'))

                                        <span class="help-block">

                                        <strong>{{ $errors->first('gstn_no') }}</strong>

                                    </span>

                                    @endif

                                </div>

                            </div>

                        </div>


                        <div class="form-group">

                            <div class="col-md-offset-2 col-md-10">

                                <button type="submit" class="btn green">Submit</button>

                                <a href="{{route('admin.franchises.index')}}" class="btn red">Cancel</a>

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

            $("#frmFranchise").validate({

                rules: {

                    name: {

                        required: true,

                        maxlength: 80,

                        not_empty: true,

                    },

                    mobile_no: {

                        required: true,

                        maxlength: 10,

                        minlength: 10,

                    },
                    password: {

                        required: true,

                        maxlength: 16,

                        minlength: 8,

                    },

                    address: {

                        required: true,

                    },

                    city_id: {

                        required: true,

                    },


                    email: {

                        required: true,

                        maxlength: 80,

                        valid_email: true,

                    },


                },

                messages: {

                    name: {

                        required: "@lang('validation.required',['attribute'=>'name'])",

                        maxlength: "@lang('validation.max.string',['attribute'=>'name','max'=>80])",

                        not_empty: "@lang('validation.not_empty',['attribute'=>'name'])",

                    },

                    mobile_no: {

                        required: "@lang('validation.required',['attribute'=>'mobile_no'])",

                        maxlength: "@lang('validation.max.string',['attribute'=>'mobile_no','max'=>80])",

                        minlength: "@lang('validation.minstring',['attribute'=>'mobile_no'])",

                    },

                    email: {

                        required: "@lang('validation.required',['attribute'=>'email'])",

                        maxlength: "@lang('validation.max.string',['attribute'=>'email','max'=>80])",

                        valid_email: "@lang('validation.email',['attribute'=>'email'])",

                    },

                    password: {

                        required: "@lang('validation.required',['attribute'=>'password'])",

                        maxlength: "@lang('validation.max.string',['attribute'=>'password','max'=>16])",

                        minlength: "@lang('validation.minstring',['attribute'=>'password'])",

                    },

                    address: {

                        required: "@lang('validation.required',['attribute'=>'address'])",

                    },

                    city_id: {

                        required: "@lang('validation.required',['attribute'=>'city'])",

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


            $("#frmFranchise").submit(function () {

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
