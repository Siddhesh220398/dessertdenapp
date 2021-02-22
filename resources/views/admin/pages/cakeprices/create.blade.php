@extends('admin.layouts.app')

@section('breadcrumb')
{!! Breadcrumbs::render('add_cakeprices') !!}
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
                <form id="frmPrices" class="form-horizontal" role="form" method="POST" action="{{ route('admin.cakeprices.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="form-group" >
                        <label for="design" class="col-md-2 control-label">{!! $mend_sign !!}Type</label>
                        <div class="col-md-6">
                           <select class="mdb-select form-control type" name="types" id="types">
                               <option value="">Select Type</option>
                               @foreach($types as $type)
                               <option value="{{$type->id}}">{{$type->name}}</option>
                               @endforeach                       
                           </select>
                       </div>
                   </div>


                    <div class="form-group{{ $errors->has('cat_name') ? ' has-error' : '' }}">
                        <label for="cat_name" class="col-md-2 control-label">{!! $mend_sign !!}Name</label>
                        <div class="col-md-6">
                            <div class="input-icon">
                                <i class="fa fa-map-marker"></i>
                                <input type="text" class="form-control" name="cat_name" id="cat_name" placeholder="Enter Name"  value="{{ old('cat_name') }}">
                                @if ($errors->has('cat_name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('cat_name') }}</strong>
                                </span>
                                @endif 
                            </div>
                        </div>
                    </div>  

                    <div class="form-group{{ $errors->has('price') ? ' has-error' : '' }}">
                        <label for="price" class="col-md-2 control-label">{!! $mend_sign !!}Price</label>
                        <div class="col-md-6">
                            <div class="input-icon">
                                <i class="fa fa-map-marker"></i>
                                <input type="text" class="form-control" name="price" id="price" placeholder="Enter Price"  value="{{ old('price') }}">
                                @if ($errors->has('price'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('price') }}</strong>
                                </span>
                                @endif 
                            </div>
                        </div>
                    </div>   

                    <div class="form-group">
                        <div class="col-md-offset-2 col-md-10">
                            <button type="submit" class="btn green">Submit</button>
                            <a href="{{route('admin.cakeprices.index')}}" class="btn red">Cancel</a>
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

       $("#frmPrices").validate({
        rules: {
            name:{
                required:true,
                
            },
            price:{
                required:true,                
                
            },
        },
        messages: {
            name:{
                required:"@lang('validation.required',['attribute'=>'name'])",
                },
            price:{
                required:"@lang('validation.required',['attribute'=>'price'])",
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

       $("#frmPrices").submit(function(){
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