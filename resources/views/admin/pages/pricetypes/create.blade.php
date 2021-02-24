@extends('admin.layouts.app')

@section('breadcrumb')
{!! Breadcrumbs::render('add_pricetypes') !!}
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
                <form id="frmPrices" class="form-horizontal" role="form" method="POST" action="{{ route('admin.pricetypes.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">
                        <label for="type" class="col-md-2 control-label">{!! $mend_sign !!}Title</label>
                        <div class="col-md-6">
                            <div class="input-icon">
                                <i class="fa fa-map-marker"></i>
                                <input type="text" class="form-control" name="type" id="type" placeholder="Enter Type"  value="{{ old('type') }}">
                                @if ($errors->has('type'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('type') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                    </div>
                    <div class="form-group{{ $errors->has('percentage') ? ' has-error' : '' }}">
                        <label for="type" class="col-md-2 control-label">{!! $mend_sign !!}Percentage</label>
                        <div class="col-md-6">
                            <div class="input-icon">
                                <i class="fa fa-map-marker"></i>
                                <input type="text" class="form-control" name="percentage" id="type" placeholder="Enter Percentage"  value="{{ old('percentage') }}">
                                @if ($errors->has('percentage'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('percentage') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                    </div>



                    <div class="form-group">
                        <div class="col-md-offset-2 col-md-10">
                            <button type="submit" class="btn green">Submit</button>
                            <a href="{{route('admin.pricetypes.index')}}" class="btn red">Cancel</a>
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

       $("#frmPrices").validate({
        rules: {
            type:{
                required:true,
            },
        },
        messages: {
            type:{
                required:"@lang('validation.required',['attribute'=>'type'])",
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

       $("#frmPrices").submit(function(){
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
