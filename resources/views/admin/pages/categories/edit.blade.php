@extends('admin.layouts.app')



@section('breadcrumb')

{!! Breadcrumbs::render('edit_category', $category) !!}

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

                <form id="frmCategory" class="form-horizontal" role="form" method="POST" action="{{ route('admin.categories.update', $category->id) }}" enctype="multipart/form-data">

                    @csrf

                    @method('PUT')

                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">

                        <label for="name" class="col-md-2 control-label">{!! $mend_sign !!}Name</label>

                        <div class="col-md-6">

                            <div class="input-icon">

                                <i class="fa fa-map-marker"></i>

                                <input type="text" class="form-control" name="name" id="name" placeholder="Enter name" maxlength="80" value="{{ old('name', $category->name) }}">

                                @if ($errors->has('name'))

                                    <span class="help-block">

                                        <strong>{{ $errors->first('name') }}</strong>

                                    </span>

                                @endif

                            </div>

                        </div>

                    </div>

                    <div class="form-group{{ $errors->has('sequence') ? ' has-error' : '' }}">

                        <label for="name" class="col-md-2 control-label">{!! $mend_sign !!}Sequence</label>

                        <div class="col-md-6">

                            <div class="input-icon">

                                <i class="fa fa-map-marker"></i>

                                <input type="number" class="form-control" name="sequence" id="sequence" placeholder="Enter sequence" maxlength="10" value="{{ old('sequence', $category->sequence) }}">

                                @if ($errors->has('sequence'))

                                    <span class="help-block">

                                        <strong>{{ $errors->first('sequence') }}</strong>

                                    </span>

                                @endif

                            </div>

                        </div>

                    </div>

                    <div class="form-group" >
                        <label for="type" class="col-md-2 control-label">{!! $mend_sign !!}Type</label>
                        <div class="col-md-6">

                            <select class="mdb-select form-control" name="type" id="type">

                                <option value="">Select Category</option>

                               <option value="cake" @if($category->type ==='cake') selected @endif> Cake</option>
                               <option value="bakery" @if($category->type ==='bakery') selected @endif>Bakery</option>
                               <option value="other" @if($category->type ==='other') selected @endif>Others</option>


                            </select>

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

                        <div class="col-md-offset-2 col-md-2">

                            <img src="{{ Storage::url($category->image) }}" alt="Image" class="img-thumbnail" />

                        </div>

                    </div>



                    <div class="form-group">

                        <div class="col-md-offset-2 col-md-10">

                            <button type="submit" class="btn green">Submit</button>

                            <a href="{{route('admin.categories.index')}}" class="btn red">Cancel</a>

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

    $("#frmCategory").validate({

        rules: {

            name:{

                required:true,

                maxlength:80,

                not_empty:true,

            },
            sequence:{

                required:true,

                maxlength:10,

                not_empty:true,

            },

        },

        messages: {

            name:{

                required:"@lang('validation.required',['attribute'=>'name'])",

                maxlength:"@lang('validation.max.string',['attribute'=>'name','max'=>80])",

                not_empty:"@lang('validation.not_empty',['attribute'=>'name'])",

            },
            sequence:{

                required:"@lang('validation.required',['attribute'=>'sequence'])",

                maxlength:"@lang('validation.max.string',['attribute'=>'sequence','max'=>10])",

                not_empty:"@lang('validation.not_empty',['attribute'=>'sequence'])",

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



    $("#frmCategory").submit(function(){

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
