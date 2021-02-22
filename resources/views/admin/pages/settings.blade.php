@extends('admin.layouts.app')

@section('breadcrumb')
{!! Breadcrumbs::render('settings') !!}
@endsection

@section('content')
 <div class="row ">
    <div class="col-md-12">
        <!-- BEGIN SAMPLE FORM PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-lock font-green"></i>
                    <span class="caption-subject font-green bold uppercase">Settings</span>
                </div>
            </div>
            <div class="portlet-body">
                <form class="form-horizontal" id="frmSettings" role="form" method="POST" action="{{ route('admin.settings.changesetting') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group{{ $errors->has('live_date') ? ' has-error' : '' }}">
                        <label for="name" class="col-md-2 control-label">Date to live{!! $mend_sign !!}</label>
                        <div class="col-md-4">
                            <div class="input-icon">
                                <i class="fa fa-calendar"></i>
                                <input type="text" placeholder="Enter date to live" name="live_date" id="live_date" class="form-control" value="{{old('live_date', $setting['live_date'])}}" readonly />
                                @if ($errors->has('live_date'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('live_date') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('guest_limit') ? ' has-error' : '' }}">
                        <label for="name" class="col-md-2 control-label">Guest Limit{!! $mend_sign !!}</label>
                        <div class="col-md-4">
                            <div class="input-icon">
                                <i class="fa fa-users"></i>
                                <input type="number" placeholder="Enter guest limit" name="guest_limit" id="guest_limit" class="form-control" value="{{old('guest_limit', $setting['guest_limit'])}}" />
                                @if ($errors->has('guest_limit'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('guest_limit') }}</strong>
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
        $('#live_date').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
        });

        $('#frmSettings').validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                live_date:{
                    required:true,
                },
                guest_limit:{
                    required:true,
                    digits:true,
                },
            },

            messages: {
                live_date:{
                    required:"@lang('validation.required',['attribute'=>'date to live'])",
                },
                guest_limit:{
                    required:"@lang('validation.required',['attribute'=>'guest limit'])",
                    digits:"@lang('validation.numeric',['attribute'=>'guest limit'])",
                },
            },

            invalidHandler: function (event, validator) { //display error alert on form submit   
                $('.alert-danger', $('.login-form')).show();
            },

            highlight: function (element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },

            success: function (label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },

            errorPlacement: function (error, element) {
                error.insertAfter(element.closest('.input-icon'));
            },

            submitHandler: function (form) {
                form.submit();
            }
        }); 
    });
</script>
@endpush