@extends('admin.layouts.app')

@section('breadcrumb')
{!! Breadcrumbs::render('importproduct') !!}
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
                <form id="frmproducts" class="form-horizontal" role="form" method="POST" action="{{ route('admin.products.imports') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label for="image" class="col-md-2 control-label">Excel File</label>
                        <div class="col-md-6">
                            <div class="input-icon">
                                <i class="fa fa-upload"></i>
                                <input type="file" class="form-control" name="importfile" id="importfile">

                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-offset-2 col-md-10">
                            <button type="submit" class="btn green">Submit</button>
                            <a href="{{route('admin.products.index')}}" class="btn red">Cancel</a>
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

        var i=1;


        $("#frmproducts").validate({
            rules: {
                importfile:{
                    required:true,
                },

            },
            messages: {
                importfile:{
                      required:"@lang('validation.required',['attribute'=>'importfile'])",
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

        $("#frmproducts").submit(function(){
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
