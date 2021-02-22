@extends('admin.layouts.app')

@section('breadcrumb')
{!! Breadcrumbs::render('add_coupons') !!}
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
                <form id="frmCoupon" class="form-horizontal" role="form" method="POST" action="{{ route('admin.coupons.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        <label for="name" class="col-md-2 control-label">{!! $mend_sign !!}Name</label>
                        <div class="col-md-6">
                            <div class="input-icon">
                                <i class="fa fa-map-marker"></i>
                                <input type="text" class="form-control" name="name"  placeholder="Enter name" maxlength="80" value="{{ old('name') }}">
                                @if ($errors->has('name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                                @endif 
                            </div>
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('code') ? ' has-error' : '' }}">
                        <label for="name" class="col-md-2 control-label">{!! $mend_sign !!}Code</label>
                        <div class="col-md-6">
                            <div class="input-icon">
                                <i class="fa fa-map-marker"></i>
                                <input type="text" class="form-control" name="code"  placeholder="Enter Code" maxlength="80" value="{{ old('code') }}">
                                @if ($errors->has('code'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('code') }}</strong>
                                </span>
                                @endif 
                            </div>
                        </div>
                    </div>

                    <div class="form-group" >
                        <label for="type" class="col-md-2 control-label">{!! $mend_sign !!}Type</label>
                        <div class="col-md-6">
                            <select class="form-control"  name="type" >
                                <option value="">Select Type</option>
                                <option value="percentage">Percentage</option>
                                <option value="fixed">Fixed</option>

                            </select>
                            @if ($errors->has('type'))
                            <span class="help-block">
                                <strong>{{ $errors->first('type') }}</strong>
                            </span>
                            @endif 
                        </div>
                    </div>
                    <div class="form-group{{ $errors->has('value') ? ' has-error' : '' }}">
                        <label for="value" class="col-md-2 control-label">{!! $mend_sign !!}Value</label>
                        <div class="col-md-6">
                            <div class="input-icon">
                                <i class="fa fa-map-marker"></i>
                                <input type="text" class="form-control" name="value"  placeholder="Enter Value" maxlength="80" value="{{ old('value') }}">
                                @if ($errors->has('value'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('value') }}</strong>
                                </span>
                                @endif 
                            </div>
                        </div>
                    </div>



                    <div class="form-group{{ $errors->has('expiryDate') ? ' has-error' : '' }}">
                        <label for="expiryDate" class="col-md-2 control-label">{!! $mend_sign !!}Expiry Date</label>
                        <div class="col-md-6">
                            <div class="input-icon">
                                <i class="fa fa-calendar"></i>
                                <input type="text" class="form-control"  id="expiryDate" name="expiryDate"  placeholder="Enter Expiry Date"  value="{{ old('expiryDate') }}">
                                @if ($errors->has('expiryDate'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('expiryDate') }}</strong>
                                </span>
                                @endif 
                            </div>
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('maxCoupon') ? ' has-error' : '' }}">
                        <label for="maxCoupon" class="col-md-2 control-label">{!! $mend_sign !!}Max Coupons</label>
                        <div class="col-md-6">
                            <div class="input-icon">
                                <i class="fa fa-map-marker"></i>
                                <input type="text" class="form-control" name="maxCoupon"  placeholder="Enter Max Coupons"  value="{{ old('maxCoupon') }}">
                                @if ($errors->has('maxCoupon'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('maxCoupon') }}</strong>
                                </span>
                                @endif 
                            </div>
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('userAllowed') ? ' has-error' : '' }}">
                        <label for="userAllowed" class="col-md-2 control-label">{!! $mend_sign !!}User Allowed</label>
                        <div class="col-md-6">
                            <div class="input-icon">
                                <i class="fa fa-map-marker"></i>
                                <input type="text" class="form-control" name="userAllowed"  placeholder="Enter User Allowed"  value="{{ old('userAllowed') }}">
                                @if ($errors->has('userAllowed'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('userAllowed') }}</strong>
                                </span>
                                @endif 
                            </div>
                        </div>
                    </div>




                    <div class="form-group">
                        <div class="col-md-offset-2 col-md-10">
                            <button type="submit" class="btn green">Submit</button>
                            <a href="{{route('admin.coupons.index')}}" class="btn red">Cancel</a>
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

         $('#expiryDate').datepicker({
        "format": 'dd-mm-yyyy',
        "setDate": new Date(),
        "todayHighlight": true,
        "autoclose": true
      });
        $("#frmCoupon").validate({
            rules: {
                name:{
                    required:true,
                    maxlength:80,
                    not_empty:true,
                },
                code:{
                    required:true,                
                },
                expiryDate:{
                    required:true,                
                },
                password:{
                    required:true,                
                },
                value:{
                    required:true,

                },
                type:{
                    required:true
                },
                expiryDate:{
                    required:true
                },
                maxCoupon:{
                    required:true
                },
                userAllowed:{
                    required:true
                }                                             
            },
            messages: {
                name:{
                    required:"@lang('validation.required',['attribute'=>'name'])",
                    maxlength:"@lang('validation.max.string',['attribute'=>'name','max'=>80])",
                    not_empty:"@lang('validation.not_empty',['attribute'=>'name'])",
                },
                code:{
                    required:"@lang('validation.required',['attribute'=>'code'])",  
                },
                expiryDate:{
                    required:"@lang('validation.required',['attribute'=>'expiryDate'])",  
                },
                password:{
                    required:"@lang('validation.required',['attribute'=>'password'])",   
                },
                maxCoupon:{
                    required:"@lang('validation.required',['attribute'=>'maxCoupon'])",   
                },
                value:{
                    required:"@lang('validation.required',['attribute'=>'value'])",   
                },
                type:{
                    required:"@lang('validation.required',['attribute'=>'type'])",        
                },
                userAllowed:{
                    required:"@lang('validation.required',['attribute'=>'userAllowed'])",        
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

        $("#frmCoupon").submit(function(){
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
