@extends('admin.layouts.app')

@section('breadcrumb')
    {!! Breadcrumbs::render('orders') !!}
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
                    <form id="frmFlavour" class="form-horizontal" role="form" method="POST" action="{{ route('admin.orders.update', $orderitem->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')


                        <div class="form-group">
                            <div class="col-md-offset-2 col-md-10">
                                <button type="submit" class="btn green">Submit</button>
                                <a href="{{route('admin.orders.index')}}" class="btn red">Cancel</a>
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
@endpush
