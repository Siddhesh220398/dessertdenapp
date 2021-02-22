@extends('admin.layouts.app')

@section('breadcrumb')
{!! Breadcrumbs::render('add_user') !!}
@endsection

@section('content')
 <div class="row ">
    <div class="col-md-12">
        <!-- BEGIN SAMPLE FORM PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa {{ $icon }} font-green"></i>
                    <span class="caption-subject font-green bold uppercase">Add User</span>
                </div>
            </div>
            <div class="portlet-body">
                <form id="frmUser" class="form-horizontal" role="form" method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        <label for="name" class="col-md-2 control-label">{!! $mend_sign !!}Name</label>
                        <div class="col-md-6">
                            <div class="input-icon">
                                <i class="fa fa-map-marker"></i>
                                <input type="text" class="form-control" name="name" id="name" placeholder="Enter name" maxlength="80" value="{{ old('name') }}">
                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif 
                            </div>
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <label for="name" class="col-md-2 control-label">{!! $mend_sign !!}Email Address</label>
                        <div class="col-md-6">
                            <div class="input-icon">
                                <i class="fa fa-phone"></i>
                                <input type="email" class="form-control" name="email" id="email" placeholder="Enter email" maxlength="80" value="{{ old('email') }}">
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif 
                            </div>
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                        <label for="name" class="col-md-2 control-label">Phone</label>
                        <div class="col-md-6">
                            <div class="input-icon">
                                <i class="fa fa-phone"></i>
                                <input type="text" class="form-control" name="phone" id="phone" placeholder="Enter phone" maxlength="80" value="{{ old('phone') }}">
                                @if ($errors->has('phone'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                @endif 
                            </div>
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <label for="name" class="col-md-2 control-label">{!! $mend_sign !!}Password</label>
                        <div class="col-md-6">
                            <div class="input-icon">
                                <i class="fa fa-key"></i>
                                <input type="password" class="form-control" name="password" id="password" placeholder="Enter password" maxlength="80" value="{{ old('password') }}">
                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif 
                            </div>
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                        <label for="name" class="col-md-2 control-label">{!! $mend_sign !!}Confirm Password</label>
                        <div class="col-md-6">
                            <div class="input-icon">
                                <i class="fa fa-key"></i>
                                <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="Enter password confirmation" maxlength="80" value="{{ old('password_confirmation') }}">
                                @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif 
                            </div>
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('profile') ? ' has-error' : '' }}">
                        <label for="name" class="col-md-2 control-label">Profile</label>
                        <div class="col-md-6">
                            <div class="input-icon">
                                <i class="fa fa-upload"></i>
                                <input type="file" class="form-control" name="profile" id="profile" accept=".jpg,.jpeg,.png">
                                @if ($errors->has('profile'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('profile') }}</strong>
                                    </span>
                                @endif 
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-offset-2 col-md-10">
                            <button type="submit" class="btn green">Submit</button>
                            <a href="{{route('admin.users.index')}}" class="btn red">Cancel</a>
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

$(document).ready(function() {
    $("#frmUser").validate({
        rules: {
            name:{
                required:true,
                maxlength:80,
                not_empty:true,
            },
            email:{
                required:true,
                maxlength:80,
                valid_email:true,
            },
            phone:{
                required:true,
                not_empty:true,
                digits:true,
            },
            password:{
                required:true,
                not_empty:true,
            },
            password_confirmation:{
                required:true,
                not_empty:true,
            },
            profile:{
                // required:true,
            }
        },
        messages: {
            name:{
                required:"@lang('validation.required',['attribute'=>'name'])",
                maxlength:"@lang('validation.max.string',['attribute'=>'name','max'=>80])",
                not_empty:"@lang('validation.not_empty',['attribute'=>'name'])",
            },
            email:{
                required:"@lang('validation.required',['attribute'=>'email'])",
                maxlength:"@lang('validation.max.string',['attribute'=>'email','max'=>80])",
                valid_email:"@lang('validation.email',['attribute'=>'email'])",
            },
            phone:{
                required:"@lang('validation.required',['attribute'=>'phone'])",
                not_empty:"@lang('validation.not_empty',['attribute'=>'phone'])",
                digits:"@lang('validation.numeric',['attribute'=>'phone'])",
            },
            password:{
                required:"@lang('validation.required',['attribute'=>'password'])",
                not_empty:"@lang('validation.not_empty',['attribute'=>'password'])",
            },
            password_confirmation:{
                required:"@lang('validation.required',['attribute'=>'confirm password'])",
                not_empty:"@lang('validation.not_empty',['attribute'=>'confirm password'])",
            },
            profile:{
                required:"@lang('validation.required',['attribute'=>'image'])",
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

    $("#frmUser").submit(function(){
        if($(this).valid()){
            addOverlay();
            return true;
        }
        else{
            return false;
        }
    });
});

</script>
@endpush