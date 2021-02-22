@extends('admin.layouts.app')



@section('breadcrumb')

    {!! Breadcrumbs::render('edit_balances', $balance) !!}

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

                    <form id="frmCategory" class="form-horizontal" role="form" method="POST"
                          action="{{ route('admin.balances.update', $balance->id) }}" enctype="multipart/form-data">

                        @csrf

                        @method('PUT')

                        <div class="form-group{{ $errors->has('bank') ? ' has-error' : '' }}">
                            <label for="serial" class="col-md-2 control-label">{!! $mend_sign !!}Bank/Cash</label>
                            <div class="col-md-6">
                                <div class="input-icon">
                                    <i class="fa fa-map-marker"></i>
                                    <input type="text" class="form-control date" name="bank"
                                           value="{{ old('bank',$balance->bank) }}" readonly>

                                    @if ($errors->has('bank'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('bank') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">
                            <label for="serial" class="col-md-2 control-label">{!! $mend_sign !!}Db/Cr</label>
                            <div class="col-md-6">
                                <div class="input-icon">
                                    <i class="fa fa-map-marker"></i>
                                    <select class="mdb-select form-control" name="type" id="type" readonly>
                                        <option value="Credit" @if($balance->credit!=0) selected @endif>Credit</option>
                                        <option value="Debit" @if($balance->debit!=0) selected @endif>Debit</option>

                                    </select>
                                    @if ($errors->has('type'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('type') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('franchise_id') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-2 control-label">Franchise</label>
                            <div class="col-md-6">
                                <div class="input-icon">
                                    <i class="fa fa-home"></i>
                                    <select class="mdb-select form-control" name="franchise_id" id="franchise_id"
                                            readonly>
                                        @foreach($franchises as $franchise)
                                            <option value="{{$franchise->id}}"
                                                    @if($balance->franchise_id==$franchise->id) selected @endif>{{$franchise->name}}
                                                |@if($franchise->balance<0)
                                                    {{($franchise->balance * -1).' DB' }}  @else {{($franchise->balance) .' Cr'}}  @endif </option>

                                        @endforeach

                                    </select> @if ($errors->has('franchise_id'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('franchise_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('date') ? ' has-error' : '' }}">
                            <label for="date" class="col-md-2 control-label">Date</label>
                            <div class="col-md-6">
                                <div class="input-icon">
                                    <i class="fa fa-calendar"></i>
                                    <input type="date" class="form-control date" name="date"
                                           value="{{ old('date',$balance->date) }}" readonly>
                                    @if ($errors->has('date'))
                                        <span class="help-block">
                                    <strong>{{ $errors->first('date') }}</strong>
                                </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('cash_type') ? ' has-error' : '' }}">
                            <label for="cash_type" class="col-md-2 control-label">{!! $mend_sign !!}Cash|Cheque</label>
                            <div class="col-md-6">
                                <div class="input-icon">
                                    <i class="fa fa-map-marker"></i>
                                    <input type="text" class="form-control cash_type" name="cash_type"
                                           value="{{ old('cash_type',$balance->cash_type) }}" readonly>
                                    @if ($errors->has('cash_type'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('cash_type') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>



                        <div class="form-group{{ $errors->has('cash') ? ' has-error' : '' }}">
                            <label for="cash" class="col-md-2 control-label">Amount</label>
                            <div class="col-md-6">
                                <div class="input-icon">
                                    <i class="fa fa-dollar"></i>
                                    <input type="number" class="form-control date" name="cash"
                                           value="{{ old('cash',$b) }}">
                                    @if ($errors->has('cash'))
                                        <span class="help-block">
                                    <strong>{{ $errors->first('cash') }}</strong>
                                </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('narration') ? ' has-error' : '' }}">
                            <label for="cash" class="col-md-2 control-label">Narration</label>
                            <div class="col-md-6">
                                <div class="input-icon">
                                    <i class="fa fa-file-code-o "></i>
                                    <input type="text" class="form-control narration" name="narration"
                                           value="{{ old('narration',$balance->narration) }}" readonly>
                                    @if ($errors->has('narration'))
                                        <span class="help-block">
                                    <strong>{{ $errors->first('narration') }}</strong>
                                </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-offset-2 col-md-10">
                                <button type="submit" class="btn green">Submit</button>
                                <a href="{{route('admin.balances.index')}}" class="btn red">Cancel</a>
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
            $("#frmBanners").validate({
                rules: {
                    franchise_id: {
                        required: true,

                    },
                    date: {
                        required: true,
                    }
                },
                messages: {
                    franchise_id: {
                        required: "@lang('validation.required',['attribute'=>'Franchise'])",
                    },
                    date: {
                        required: "@lang('validation.required',['attribute'=>'Date'])",
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

            $("#frmBanners").submit(function () {
                if ($(this).valid()) {
                    addOverlay();
                    return true;
                } else {
                    return false;
                }
            });
        });

    </script>

@endpush
