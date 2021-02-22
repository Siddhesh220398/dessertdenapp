@extends('admin.layouts.app')

@section('breadcrumb')
{!! Breadcrumbs::render('user_notification') !!}
@endsection

@push('page_css')
<style type="text/css">
    textarea {
        max-width: 100%;
        min-width: 100%;
        min-height: 70px;
    }
    .select2-selection.select2-selection--multiple {
        padding-left: 23px !important;
    }
    .select2.select2-container.select2-container--bootstrap {
        width: 100% !important;
    }
</style>
@endpush

@section('content')
 <div class="row ">
    <div class="col-md-12">
        <!-- BEGIN SAMPLE FORM PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa {{ $icon }} font-green"></i>
                    <span class="caption-subject font-green bold uppercase">Send Notification</span>
                </div>
            </div>
            <div class="portlet-body">
                <form id="frmNotification" class="form-horizontal" role="form" method="POST" action="{{ route('admin.users.submit_notification') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label class="col-md-2 control-label">Select Type</label>
                        <div class="col-md-6">
                            <div class="mt-radio-inline">
                                <label class="mt-radio">
                                    <input type="radio" name="type" value="all" checked> All Users
                                    <span></span>
                                </label>
                                <label class="mt-radio">
                                    <input type="radio" name="type" value="select_users"> Select Users
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" id="select_user_block" style="display: none;">
                        <label class="col-md-2 control-label">Select Users</label>
                        <div class="col-md-6">
                            <div class="input-icon">
                                <i class="fa fa-user"></i>
                                <select name="users[]" id="users" class="form-control input-sm select2" multiple>
                                    <option></option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name . " - " . $user->email }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label">Title</label>
                        <div class="col-md-6">
                            <div class="input-icon">
                                <i class="fa fa-bell"></i>
                                <input type="text" class="form-control" name="title" id="title" placeholder="Enter title" maxlength="20" value="{{ old('title') }}">
                                @if ($errors->has('title'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label">Description</label>
                        <div class="col-md-6">
                            <div class="input-icon">
                                <i class="fa fa-location-arrow"></i>
                                <textarea class="form-control" name="description" id="description" placeholder="Enter description..." maxlength="175">{{ old('description') }}</textarea>
                                @if ($errors->has('title'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
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
    $('#users').select2({ placeholder: "Select Users..." });

    $('input[name="type"]').change(function(){
        if ($(this).val() == "select_users") {
            $('#select_user_block').show();
        } else {
            $('#select_user_block').hide();
        }
    });

    $("#frmNotification").validate({
        rules: {
            description:{
                required:true,
                maxlength:175,
                not_empty:true,
            },
            title:{
                required:true,
                maxlength:20,
                not_empty:true,
            }
        },
        messages: {
            description:{
                required:"@lang('validation.required',['attribute'=>'description'])",
                maxlength:"@lang('validation.max.string',['attribute'=>'description','max'=>175])",
                not_empty:"@lang('validation.not_empty',['attribute'=>'description'])",
            },
            title:{
                required:"@lang('validation.required',['attribute'=>'title'])",
                maxlength:"@lang('validation.max.string',['attribute'=>'title','max'=>20])",
                not_empty:"@lang('validation.not_empty',['attribute'=>'title'])",
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

    $("#frmNotification").submit(function(){
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