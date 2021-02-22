@extends('admin.layouts.app')

@section('breadcrumb')
{!! Breadcrumbs::render('edit_flavours', $flavour) !!}
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
                <form id="frmFlavour" class="form-horizontal" role="form" method="POST" action="{{ route('admin.flavours.update', $flavour->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group{{ $errors->has('flavourname') ? ' has-error' : '' }}">
                        <label for="flavourname" class="col-md-2 control-label">{!! $mend_sign !!}Flavour Name</label>
                        <div class="col-md-6">
                            <div class="input-icon">
                                <i class="fa fa-map-marker"></i>
                                <input type="text" class="form-control" name="flavourname" id="flavourname"  maxlength="80" value="{{ old('flavourname', $flavour->name) }}">
                                @if ($errors->has('flavourname'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('flavourname') }}</strong>
                                    </span>
                              @endif 
                          </div>
                        </div>
                    </div>
                    <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                        <label for="description" class="col-md-2 control-label">{!! $mend_sign !!}Description</label>
                        <div class="col-md-6">
                            <div class="input-icon">
                                <i class="fa fa-map-marker"></i>
                                <input type="text" class="form-control" name="description" id="description"  maxlength="80" value="{{ old('description', $flavour->description) }}">
                                @if ($errors->has('description'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('description') }}</strong>
                                    </span>
                              @endif 
                          </div>
                        </div>
                    </div>    
                    <div class="form-group{{ $errors->has('rate') ? ' has-error' : '' }}">
                        <label for="rate" class="col-md-2 control-label">{!! $mend_sign !!}Rate</label>
                        <div class="col-md-6">
                            <div class="input-icon">
                                <i class="fa fa-map-marker"></i>
                                <input type="text" class="form-control" name="rate" id="rate"  maxlength="80" value="{{ old('rate', $flavour->rate) }}">
                                @if ($errors->has('rate'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('rate') }}</strong>
                                    </span>
                              @endif 
                          </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-offset-2 col-md-10">
                            <button type="submit" class="btn green">Submit</button>
                            <a href="{{route('admin.flavours.index')}}" class="btn red">Cancel</a>
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
    $("#frmFlavour").validate({
        rules: {
            flavourname:{
                required:true,
                maxlength:80,
                not_empty:true,
            },  
            description:{
                required:true,               
                not_empty:true,
            }, 
            rate:{
                required:true,               
                not_empty:true,
            },
        },
        messages: {
            flavourname:{
                required:"@lang('validation.required',['attribute'=>'flavourname'])",
                maxlength:"@lang('validation.max.string',['attribute'=>'flavourname','max'=>80])",
                not_empty:"@lang('validation.not_empty',['attribute'=>'flavourname'])",
            },  
            description:{
                required:"@lang('validation.required',['attribute'=>'description'])",
                not_empty:"@lang('validation.not_empty',['attribute'=>'description'])",
            },  
            rate:{
                required:"@lang('validation.required',['attribute'=>'rate'])",               
                not_empty:"@lang('validation.not_empty',['attribute'=>'rate'])",
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

    $("#frmFlavour").submit(function(){
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