@extends('admin.layouts.app')

@section('breadcrumb')
{!! Breadcrumbs::render('change_pass') !!}
@endsection

@section('content')
 <div class="row ">
    <div class="col-md-12">
        <!-- BEGIN SAMPLE FORM PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-lock font-green"></i>
                    <span class="caption-subject font-green sbold uppercase">Change Password</span>
                </div>
            </div>
            <div class="portlet-body">
                <form class="form-horizontal" id="frmChangepass" role="form" method="POST" action="{{ route('admin.changepass') }}">
                    {{ csrf_field() }}
                    <div class="form-group{{ $errors->has('old_password') ? ' has-error' : '' }}">
                        <label for="old_password" class="col-md-2 control-label">{!! $mend_sign !!}Current Password</label>
                        <div class="col-md-6">
                            <div class="input-icon">
                                <i class="fa fa-lock"></i>
                                <input type="password" class="form-control" name="old_password" id="old_password" placeholder="Old Password">
                                @if ($errors->has('old_password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('old_password') }}</strong>
                                    </span>
                                @endif 
                            </div>
                        </div>
                    </div>
                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <label for="password" class="col-md-2 control-label">{!! $mend_sign !!}New Password</label>
                        <div class="col-md-6">
                            <div class="input-icon">
                                <i class="fa fa-lock"></i>
                                <input type="password" class="form-control" name="password" id="password" placeholder="New Password">
                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif 
                            </div>
                        </div>
                    </div>
                    <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                        <label for="password_confirmation" class="col-md-2 control-label">{!! $mend_sign !!}Confirm Password</label>
                        <div class="col-md-6">
                            <div class="input-icon">
                                <i class="fa fa-lock"></i>
                                <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password"> 
                                @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-offset-2 col-md-10">
                            <button type="submit" class="btn green">Submit</button>
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
    $(function(){
        $('#frmChangepass').validate({
            errorElement: 'span',
            errorClass: 'help-block',
            focusInvalid: false,
            rules: {
                old_password: {
                    required: true,
                    minlength:6,
                    remote: {
                        url: "{{ route('admin.checkoldpass') }}",
                        type: "post",
                        data: {
                            _token: "{{ csrf_token()}}",
                            current_password: function() {
                                return $( "#old_password" ).val();
                            },
                            email: "{{ Auth::user()->email }}",
                        }
                    }
                },
                password: {
                    required: true,
                    not_equal: "#old_password",
                    no_space:true,
                    minlength:6,
                    maxlength:15,
                },
                password_confirmation:{
                    required: true,
                    no_space: true,
                    equalTo:"#password"
                }
            },
            messages: {
                old_password: {
                    required: "@lang('validation.required',['attribute'=>'current password'])",
                    minlength:"@lang('validation.min.string',['attribute'=>'current password','min'=>6])",
                    remote: "@lang('validation.not_match', ['attribute'=>'current password'])",
                },
                password: {
                    required:"@lang('validation.required',['attribute'=>'password'])",
                    not_equal:"@lang('validation.not_equal',['attribute'=>'password', 'other' => 'current password'])",
                    no_space:"@lang('validation.no_space',['attribute'=>'password'])",
                    minlength:"@lang('validation.min.string',['attribute'=>'password','min'=>6])",
                    maxlength:"@lang('validation.max.string',['attribute'=>'password','max'=>15])"
                },
                password_confirmation:{
                    required: "@lang('validation.required',['attribute'=>'confirm password'])",
                    no_space:"@lang('validation.no_space',['attribute'=>'confirm password'])",
                    equalTo:"@lang('validation.same',['attribute'=>'password','other'=>'confirm password'])"
                }
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
            },
            submitHandler: function(form) {  
                if($("#frmChangepass").valid()) {
                    $(form)[0].submit();
                }
            }
        }); 
    });
</script>
@endpush