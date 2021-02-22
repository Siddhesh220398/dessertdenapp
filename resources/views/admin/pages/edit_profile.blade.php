@extends('admin.layouts.app')

@section('breadcrumb')
{!! Breadcrumbs::render('my_profile') !!}
@endsection

@push('page_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('theme/css/profile.css') }}">
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN PROFILE SIDEBAR -->
        <div class="profile-sidebar">
            <!-- PORTLET MAIN -->
            <div class="portlet light profile-sidebar-portlet bordered">
                <!-- SIDEBAR USERPIC -->
                <div class="profile-userpic">
                    <img src="{{ $user->profile }}" class="img-responsive" alt="">
                </div>
                <!-- END SIDEBAR USERPIC -->
                <!-- SIDEBAR USER TITLE -->
                <div class="profile-usertitle">
                    <div class="profile-usertitle-name"> {{$user->name}} </div>
                </div>
                <!-- END SIDEBAR USER TITLE -->
            </div>
            <!-- END PORTLET MAIN -->
        </div>
        <!-- END BEGIN PROFILE SIDEBAR -->
        <!-- BEGIN PROFILE CONTENT -->
        <div class="profile-content">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light bordered">
                        <div class="portlet-title tabbable-line">
                            <div class="caption caption-md">
                                <i class="icon-globe theme-font hide"></i>
                                <span class="caption-subject font-blue-madison bold uppercase">Profile Account</span>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="tab-content">
                                <!-- PERSONAL INFO TAB -->
                                <div class="tab-pane active" id="tab_1_1">
                                    <form role="form" id="frmProfile" method="POST" action="{{ route('admin.editProfile') }}" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                            <label class="control-label">{!! $mend_sign !!}Name</label>
                                            <div class="input-icon">
                                                <i class="fa fa-font"></i>
                                                <input type="text" placeholder="Enter Name" name="name" id="name" class="form-control" value="{{old('name',$user->name)}}" maxlength="50" /> 
                                                @if ($errors->has('name'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('name') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                            <label class="control-label">{!! $mend_sign !!}Email</label>
                                            <div class="input-icon">
                                                <i class="fa fa-envelope"></i>
                                                <input type="email" placeholder="Enter E-mail Address" name="email" id="email" class="form-control" value="{{old('email',$user->email)}}" maxlength="150" /> 
                                                @if ($errors->has('email'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('email') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('mobile') ? ' has-error' : '' }}">
                                            <label class="control-label">{!! $mend_sign !!}Contact Number</label>
                                            <div class="input-icon">
                                                <i class="fa fa-phone"></i>
                                                <input type="text" placeholder="Enter Contact No" name="mobile" id="mobile" class="form-control" value="{{old('mobile',$user->phone)}}" maxlength="10" /> 
                                                @if ($errors->has('mobile'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('mobile') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('profile') ? ' has-error' : '' }}">
                                            <label class="control-label">Profile Picture</label>
                                            <div class="input-icon">
                                                <i class="fa fa-upload"></i>
                                                <input type="file" name="profile" id="profile" class="form-control" /> 
                                                @if ($errors->has('profile'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('profile') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="margiv-top-10">
                                            <button type="submit" class="btn green"> Save Changes </button>
                                        </div>
                                    </form>
                                </div>
                                <!-- END PERSONAL INFO TAB -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PROFILE CONTENT -->
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">

    $(function(){
        $('#frmProfile').validate({
            errorElement: 'span',
            errorClass: 'help-block',
            focusInvalid: false,
            rules: {
                name:{
                    required:true,
                    not_empty:true,
                    maxlength:50
                },
                email:{
                    required:true,
                    valid_email:true,
                    maxlength:80,
                    remote: {
                        url: "{{ route('admin.uniqueAdminemail') }}",
                        type: "post",
                        data: {
                            _token: function() {
                                return "{{csrf_token()}}"
                            },
                            email: function(){
                                return $("#email").val();
                            },
                            id: function(){
                                return "{{$user->id}}"
                            }
                        }
                    },
                },
                mobile:{
                    required:true,
                    digits:true,
                    minlength:10,
                    maxlength:10
                },
                profile:{
                    extension:'jpg|png|jpeg'
                },
            },
            messages: {
                name:{
                    required:"@lang('validation.required',['attribute'=>'name'])",
                    maxlength:"@lang('validation.max.string',['attribute'=>'name','max'=>50])"
                },
                email:{
                    required:"@lang('validation.required',['attribute'=>'email address'])",
                    email: "@lang('validation.email', ['attribute'=>'email address'])",
                    pattern: "@lang('validation.email', ['attribute'=>'email address'])",
                    remote:"@lang('validation.unique',['attribute'=>'email address'])",
                    maxlength:"@lang('validation.max.string',['attribute'=>'email address','max'=>80])"
                },
                mobile:{
                    required:"@lang('validation.required',['attribute'=>'mobile number'])",
                    minlength:"@lang('validation.min.string',['attribute'=>'mobile number','min'=>10])",
                    maxlength:"@lang('validation.max.string',['attribute'=>'mobile number','max'=>14])"
                },
                profile:{
                    extension:"@lang('validation.mimetypes',['attribute'=>'profile photo','value'=>'jpg|png|jpeg'])"
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
                if (element.attr("type") == "radio") {
                      error.appendTo('.a');
                }else{
                    if (element.attr("data-error-container")) {
                        error.appendTo(element.attr("data-error-container"));
                    } else {
                        error.insertAfter(element);
                    }
                }
            }
        });
        $(document).on('submit','#frmProfile',function(){
            if($("#frmProfile").valid()){
                $(this).submit(function() {
                    return false;
                });
                return true;
            }else{
                return false;
            }
        }); 
    });
</script>
@endpush