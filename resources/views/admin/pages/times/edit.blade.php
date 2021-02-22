@extends('admin.layouts.app')

@section('breadcrumb')
{!! Breadcrumbs::render('edit_times', $time) !!}
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
                <form id="frmTime" class="form-horizontal" role="form" method="POST" action="{{ route('admin.times.update', $time->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group{{ $errors->has('startingtime') ? ' has-error' : '' }}">
                        <label for="startingtime" class="col-md-2 control-label">{!! $mend_sign !!}Starting Time</label>
                        <div class="col-md-6">
                            <div class="input-icon">
                                <i class="fa fa-map-marker"></i>
                                <input type="text" class="form-control" name="startingtime" id="startingtime" placeholder="Enter startingtime" value="{{ old('startingtime', $time->startingtime) }}">
                                @if ($errors->has('startingtime'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('startingtime') }}</strong>
                                    </span>
                                @endif 
                            </div>
                        </div>
                    </div>

                     <div class="form-group{{ $errors->has('endingtime') ? ' has-error' : '' }}">
                        <label for="endingtime" class="col-md-2 control-label">{!! $mend_sign !!}Ending Time</label>
                        <div class="col-md-6">
                            <div class="input-icon">
                                <i class="fa fa-map-marker"></i>
                                <input type="text" class="form-control" name="endingtime" id="endingtime" placeholder="Enter endingtime" value="{{ old('endingtime', $time->endingtime) }}">
                                @if ($errors->has('endingtime'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('endingtime') }}</strong>
                                    </span>
                                @endif 
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group{{ $errors->has('hours') ? ' has-error' : '' }}">
                        <label for="hours" class="col-md-2 control-label">{!! $mend_sign !!}Hours </label>
                        <div class="col-md-6">
                            <div class="input-icon">
                                <i class="fa fa-map-marker"></i>
                                <input type="number" class="form-control" name="hours" id="hours"  value="{{ old('hours', $time->hours) }}">
                                @if ($errors->has('hours'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('hours') }}</strong>
                                    </span>
                                @endif 
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-offset-2 col-md-10">
                            <button type="submit" class="btn green">Submit</button>
                            <a href="{{route('admin.times.index')}}" class="btn red">Cancel</a>
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
    $("#frmTime").validate({
        rules: {
            startingtime:{
                required:true,                
                not_empty:true,
            },
            endingtime:{
                required:true,                
                not_empty:true,
            }
        },
        messages: {
            startingtime:{
                required:"@lang('validation.required',['attribute'=>'startingtime'])",                
                not_empty:"@lang('validation.not_empty',['attribute'=>'startingtime'])",
            },
            endingtime:{
                required:"@lang('validation.required',['attribute'=>'endingtime'])",                
                not_empty:"@lang('validation.not_empty',['attribute'=>'endingtime'])",
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
        }
    });

    $("#frmTime").submit(function(){
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