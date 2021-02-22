@extends('admin.layouts.app')

@section('breadcrumb')
{!! Breadcrumbs::render('add_banners') !!}
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
                <form id="frmBanners" class="form-horizontal" role="form" method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="form-group{{ $errors->has('serial') ? ' has-error' : '' }}">
                        <label for="serial" class="col-md-2 control-label">{!! $mend_sign !!}Sequence</label>
                        <div class="col-md-6">
                            <div class="input-icon">
                                <i class="fa fa-map-marker"></i>
                                <input type="text" class="form-control" name="serial" id="serial" placeholder="Enter Sequence" maxlength="10" value="{{ old('serial') }}">
                                @if ($errors->has('serial'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('serial') }}</strong>
                                    </span>
                                @endif 
                            </div>
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
                        <label for="name" class="col-md-2 control-label">image</label>
                        <div class="col-md-6">
                            <div class="input-icon">
                                <i class="fa fa-upload"></i>
                                <input type="file" class="form-control" name="image" id="image" accept=".jpg,.jpeg,.png">
                                @if ($errors->has('image'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('image') }}</strong>
                                    </span>
                                @endif 
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-offset-2 col-md-10">
                            <button type="submit" class="btn green">Submit</button>
                            <a href="{{route('admin.banners.index')}}" class="btn red">Cancel</a>
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
    $("#frmBanners").validate({
        rules: {
            serial:{
                required:true,
                maxlength:10,
                not_empty:true,
            },
            image:{
                required:true,
            }
        },
        messages: {
            name:{
                required:"@lang('validation.required',['attribute'=>'serial'])",
                maxlength:"@lang('validation.max.string',['attribute'=>'serial','max'=>10])",
                not_empty:"@lang('validation.not_empty',['attribute'=>'serial'])",
            },
            image:{
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

    $("#frmBanners").submit(function(){
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