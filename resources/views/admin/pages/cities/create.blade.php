@extends('admin.layouts.app')

@section('breadcrumb')
{!! Breadcrumbs::render('add_city') !!}
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
                <form id="frmCity" class="form-horizontal" role="form" method="POST" action="{{ route('admin.cities.store') }}" enctype="multipart/form-data">
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

                     <div class="form-group" >
                        <label for="city_type" class="col-md-2 control-label">{!! $mend_sign !!}Type</label>
                        <div class="col-md-6">
                            <select class="form-control"  name="city_type" >
                                <option value="">Select Type</option>
                                <option value="in">In</option>
                                <option value="out">Out/option>

                            </select>
                            @if ($errors->has('city_type'))
                            <span class="help-block">
                                <strong>{{ $errors->first('city_type') }}</strong>
                            </span>
                            @endif 
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-offset-2 col-md-10">
                            <button type="submit" class="btn green">Submit</button>
                            <a href="{{route('admin.cities.index')}}" class="btn red">Cancel</a>
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
    $("#frmCity").validate({
        rules: {
            name:{
                required:true,
                maxlength:80,
                not_empty:true,
            },
            city_type:{
                required:true,
               
            },
        },
        messages: {
            name:{
                required:"@lang('validation.required',['attribute'=>'name'])",
                maxlength:"@lang('validation.max.string',['attribute'=>'name','max'=>80])",
                not_empty:"@lang('validation.not_empty',['attribute'=>'name'])",
            },
            city_type:{
                required:"@lang('validation.required',['attribute'=>'city_type'])",
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

    $("#frmCity").submit(function(){
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