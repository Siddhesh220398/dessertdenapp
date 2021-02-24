@extends('admin.layouts.app')



@section('breadcrumb')

    {!! Breadcrumbs::render('edit_franchisesprice', $franchisesprice) !!}

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

                    <form id="frmFranchisesPrice" class="form-horizontal" role="form" method="POST"
                          action="{{ route('admin.franchisesprice.update', $franchisesprice->id) }}"
                          enctype="multipart/form-data">

                        @csrf

                        @method('PUT')


                        <div class="form-group{{ $errors->has('franchise') ? ' has-error' : '' }}">


                            <label for="city" class="col-md-2 control-label">Franchise</label>

                            <div class="col-md-6">

                                <div class="input-icon">

                                    <i class="fa fa-map-marker"></i>

                                    <input type="text" class="form-control" name="franchise" id="franchise"
                                           value="{{ $franchisesprice->name }} " readonly>
                                    <input type="hidden" class="form-control" name="franchise_id" id="franchise_id"
                                           value="{{ $franchisesprice->id }} ">

                                    @if ($errors->has('franchise'))

                                        <span class="help-block">

                                    <strong>{{ $errors->first('franchise') }}</strong>

                                </span>

                                    @endif

                                </div>

                            </div>

                        </div>


                        <div class="form-group">

                            <label for="city" class="col-md-2 control-label">{!! $mend_sign !!}Price Type</label>

                            <div class="col-md-6">

                                <select class="form-control pricetype_id" name="pricetype_id" id="pricetype_id">

                                    <option value="">Select Type</option>

                                    @foreach($pricetypes as $pricetype)

                                        <option value="{{$pricetype->id}}">{{$pricetype->type}}</option>

                                    @endforeach

                                </select>

                            </div>

                        </div>
                        <br>

                        <table class="col-12 table" border="1"
                               style="width:80%;height: 100%; margin-left: 100px;  text-align: center;">
                            <thead>
                            <tr>
                                <th>Category</th>
                                <th>Percentage</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($categories as $category)
                                <tr>
                                    <td>{{$category->name}}</td>
                                    <td><input type="number" class="form-control percentage"
                                               name="percentage[{{$category->id}}]" id="percentage"
                                               placeholder="Percentage"
                                               value="{{\App\Models\FranchisePrice::where(['franchise_id' =>$franchisesprice->id,'category_id'=>$category->id])->value('percentage')}}">

                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>


                        <br>


                        <div class="form-group">

                            <div class="col-md-offset-2 col-md-10">

                                <button type="submit" class="btn green">Submit</button>

                                <a href="{{route('admin.franchisesprice.index')}}" class="btn red">Cancel</a>

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


        $(document).ready(function () {

            $("#frmFranchisesPrice").validate({

                rules: {

                    franchise_id: {

                        required: true,

                    },


                },

                messages: {

                    franchise_id: {

                        required: "@lang('validation.required',['attribute'=>'franchise_id'])",

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


            $("#frmFranchisesPrice").submit(function () {

                if ($(this).valid()) {

                    addOverlay();

                    return true;

                } else {

                    return false;

                }

            });

            $(document).on("change", ".pricetype_id", function () {

                $.ajax({
                    type: "POST",
                    url: "{{route('admin.pricetype.select')}}",
                    data: {
                        '_token': $('input[name="_token"]').val(),
                        'pricetype_id': $('.pricetype_id').val()
                    },
                    success: function (data) {
                        console.log(data['percentage']);
                        $(".percentage").val("");
                        // console.log(data['percentage']);
                        $(".percentage").val(data['percentage']);

                    }
                });
            });

        });


    </script>

@endpush
