@extends('admin.layouts.app')



@section('breadcrumb')

{!! Breadcrumbs::render('add_products') !!}

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

                <form id="frmproducts" class="form-horizontal" role="form" method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">

                    @csrf



                    <div class="form-group" >

                        <label for="Flavour" class="col-md-2 control-label">{!! $mend_sign !!}Category</label>

                        <div class="col-md-6">

                            <select class="mdb-select form-control" name="subcategory_id" id="subcategory_id">

                                <option value="">Select Category</option>

                                @foreach($categories as $category)

                                <option value="{{$category->id}}" @if(old('category_id') == $category->id) selected @endif>{{$category->name}}</option>

                                @endforeach

                            </select>

                        </div>

                    </div>



                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">

                        <label for="name" class="col-md-2 control-label">{!! $mend_sign !!}Name</label>

                        <div class="col-md-6">

                            <div class="input-icon">

                                <i class="fa fa-map-marker"></i>

                                <input type="text" class="form-control" name="name" id="name" placeholder="Enter Name"  value="{{ old('name') }}">

                                @if ($errors->has('name'))

                                <span class="help-block">

                                    <strong>{{ $errors->first('name') }}</strong>

                                </span>

                                @endif

                            </div>

                        </div>

                    </div>



                    <div class="form-group{{ $errors->has('code') ? ' has-error' : '' }}">

                        <label for="code" class="col-md-2 control-label">{!! $mend_sign !!}Code</label>

                        <div class="col-md-6">

                            <div class="input-icon">

                                <i class="fa fa-map-marker"></i>

                                <input type="text" class="form-control" name="code" id="code" placeholder="Enter Code" maxlength="10" value="{{ old('code') }}">

                                @if ($errors->has('code'))

                                <span class="help-block">

                                    <strong>{{ $errors->first('code') }}</strong>

                                </span>

                                @endif

                            </div>

                        </div>

                    </div>



                    <div class="form-group">

                        <label for="image" class="col-md-2 control-label">Image</label>

                        <div class="col-md-6">

                            <div class="input-icon">

                                <i class="fa fa-upload"></i>

                                <input type="file" class="form-control" name="image" id="image" accept=".jpg,.jpeg,.png">



                            </div>

                        </div>

                    </div>



                    <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">

                        <label for="description" class="col-md-2 control-label">{!! $mend_sign !!}Description</label>

                        <div class="col-md-6">

                            <div class="input-icon">

                                <i class="fa fa-map-marker"></i>

                                <input type="text" class="form-control" name="description" id="description" placeholder="Enter Description"  value="{{ old('description') }}">

                                @if ($errors->has('description'))

                                <span class="help-block">

                                    <strong>{{ $errors->first('description') }}</strong>

                                </span>

                                @endif

                            </div>

                        </div>

                    </div>



                    <div class="form-group">

                        <label class="col-md-2 control-label" >Weight</label>

                        <div class="col-md-6">

                            <div class="input-icon">

                                <table id="dynamic_field">

                                    <tr>

                                        <td><input type="text" class="form-control weight_list"  placeholder="Enter Weight" name="weights[]" id="weight" value="{{ old('weight') }}"></td>

                                        <td><button type="button" name="add" class="btn btn-success add" id="add">Add More</button></td>

                                    </tr>

                                </table>

                            </div>

                        </div>

                    </div>



                    <div class="form-group" >

                        <label for="Flavour" class="col-md-2 control-label">{!! $mend_sign !!}Flavour</label>

                        <div class="col-md-6">

                            <select class="mdb-select form-control flavour_id" name="flavours[]" id="flavours" multiple>

                                <option value="">Select Flavour</option>

                                @foreach($flavours as $flavour)

                                <option value="{{$flavour->id}}" @if(old('flavour_id') == $flavour->id)  selected @endif>{{$flavour->name}}</option>

                                @endforeach

                            </select>

                        </div>

                    </div>





                    <div class="form-group" >

                        <label for="Default" class="col-md-2 control-label">{!! $mend_sign !!}Default Flavour</label>

                        <div class="col-md-6">

                            <select class="mdb-select form-control" name="is_default" id="is_default">

                                <option value="">Select Default Flavour</option>

                                @foreach($flavours as $flavour)

                                <option value="{{$flavour->id}}" @if(old('is_default') == $flavour->id) selected @endif>{{$flavour->name}}</option>

                                @endforeach

                            </select>

                        </div>

                    </div>







                    <div class="form-group" >

                        <label for="Type" class="col-md-2 control-label">{!! $mend_sign !!}Type</label>

                        <div class="col-md-6">

                            <select class="mdb-select form-control type_id" name="types[]" id="types" multiple>

                                <option value="">Select Type</option>

                                @foreach($prices as $price)

                                <option value="{{$price->id}}" @if(old('type_id') == $price->id) selected @endif>{{$price->name}}</option>

                                @endforeach

                            </select>

                        </div>

                    </div>



                    <div class="form-group" >

                        <label for="Type" class="col-md-2 control-label">For Franchise Only</label>

                        <div class="col-md-6">

                            <select class="mdb-select form-control type_id" name="is_franchise" id="is_franchise" multiple>

                                <option value="">Select Option</option>
                                <option value="0">Both</option>
                                <option value="1">Only Franchise</option>

                              

                            </select>

                        </div>

                    </div>





                    <div class="form-group{{ $errors->has('price') ? ' has-error' : '' }}">

                        <label for="price" class="col-md-2 control-label">{!! $mend_sign !!}Price</label>

                        <div class="col-md-6">

                            <div class="input-icon">

                                <i class="fa fa-map-marker"></i>

                                <input type="text" class="form-control" name="price" id="price" placeholder="Enter price" maxlength="10" value="{{ old('price') }}">

                                @if ($errors->has('price'))

                                <span class="help-block">

                                    <strong>{{ $errors->first('price') }}</strong>

                                </span>

                                @endif

                            </div>

                        </div>

                    </div>

                    <div class="form-group{{ $errors->has('hsn_code') ? ' has-error' : '' }}">

                        <label for="hsn_code" class="col-md-2 control-label">{!! $mend_sign !!}Hsn Code</label>

                        <div class="col-md-6">

                            <div class="input-icon">

                                <i class="fa fa-map-marker"></i>

                                <input type="text" class="form-control" name="hsn_code" id="hsn_code" placeholder="Enter Hsn Code"  value="{{ old('hsn_code') }}">

                                @if ($errors->has('hsn_code'))

                                    <span class="help-block">

                                    <strong>{{ $errors->first('hsn_code') }}</strong>

                                </span>

                                @endif

                            </div>

                        </div>

                    </div>

                    <div class="form-group{{ $errors->has('gst_price') ? ' has-error' : '' }}">

                        <label for="gst_price" class="col-md-2 control-label">{!! $mend_sign !!}GST</label>

                        <div class="col-md-6">

                            <div class="input-icon">

                                <i class="fa fa-map-marker"></i>

                                <input type="text" class="form-control" name="gst_price" id="gst_price" placeholder="Enter GST"  value="{{ old('gst_price') }}">

                                @if ($errors->has('gst_price'))

                                    <span class="help-block">

                                    <strong>{{ $errors->first('gst_price') }}</strong>

                                </span>

                                @endif

                            </div>

                        </div>

                    </div>

                    <div class="form-group{{ $errors->has('cgst') ? ' has-error' : '' }}">

                        <label for="cgst" class="col-md-2 control-label">{!! $mend_sign !!}CGST</label>

                        <div class="col-md-6">

                            <div class="input-icon">

                                <i class="fa fa-map-marker"></i>

                                <input type="text" class="form-control" name="cgst" id="cgst" placeholder="Enter CGST"  value="{{ old('cgst') }}">

                                @if ($errors->has('cgst'))

                                    <span class="help-block">

                                    <strong>{{ $errors->first('cgst') }}</strong>

                                </span>

                                @endif

                            </div>

                        </div>

                    </div>

                    <div class="form-group{{ $errors->has('sgst') ? ' has-error' : '' }}">

                        <label for="sgst" class="col-md-2 control-label">{!! $mend_sign !!}SGST</label>

                        <div class="col-md-6">

                            <div class="input-icon">

                                <i class="fa fa-map-marker"></i>

                                <input type="text" class="form-control" name="sgst" id="sgst" placeholder="Enter SGST"  value="{{ old('sgst') }}">

                                @if ($errors->has('sgst'))

                                    <span class="help-block">

                                    <strong>{{ $errors->first('sgst') }}</strong>

                                </span>

                                @endif

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

        $('#add').click(function(){

            i++;

            $('#dynamic_field').append('<tr id="row'+i+'"><td><input type="text" name="weights[]" placeholder="Enter Weight" class="form-control weight_list" /></td><td><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove">X</button></td></tr>');

        });

        $(document).on('click', '.btn_remove', function(){

            var button_id = $(this).attr("id");

            $('#row'+button_id+'').remove();

        });



        $("#frmproducts").validate({

            rules: {

                subcategory_id:{

                    required:true,

                },

                name:{

                    required:true,

                    not_empty:true,

                },

                description:{

                    required:true,

                    not_empty:true,

                },

                code:{

                    required:true,

                    maxlength:10,

                    not_empty:true,

                },

                // image:{

                //     required:true,

                // }

            },

            messages: {

                subcategory_id:{

                      required:"@lang('validation.required',['attribute'=>'subcategory_id'])",

                },

                name:{

                    required:"@lang('validation.required',['attribute'=>'name'])",

                    maxlength:"@lang('validation.max.string',['attribute'=>'name','max'=>10])",

                    not_empty:"@lang('validation.not_empty',['attribute'=>'name'])",

                },

                description:{

                    required:"@lang('validation.required',['attribute'=>'description'])",

                    not_empty:"@lang('validation.not_empty',['attribute'=>'description'])",

                },

                code:{

                    required:"@lang('validation.required',['attribute'=>'code'])",

                    maxlength:"@lang('validation.max.string',['attribute'=>'code','max'=>10])",

                    not_empty:"@lang('validation.not_empty',['attribute'=>'code'])",

                },

                // image:{

                //     required:"@lang('validation.required',['attribute'=>'image'])",

                // },

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
