@extends('admin.layouts.app')

@section('breadcrumb')
{!! Breadcrumbs::render('add_franchisesprice') !!}
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
                <form id="frmFranchisePrifranchisece" class="form-horizontal" role="form" method="POST" action="{{ route('admin.franchisesprice.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="form-group" >
                        <label for="city" class="col-md-2 control-label">{!! $mend_sign !!}Franchise</label>
                        <div class="col-md-6">
                           <select class="form-control" name="franchise_id" id="franchise_id">
                               <option value="">Select Franchise</option>
                               @foreach($franchises as $franchise)
                               <option value="{{$franchise->id}}" @if(old('franchise_id') == $franchise->id) selected @endif>{{$franchise->name}}</option>
                               @endforeach                       
                           </select>
                       </div>
                   </div>

                   <div class="form-group" >
                    <label for="city" class="col-md-2 control-label">{!! $mend_sign !!}Category</label>
                    <div class="col-md-6">
                     <select class="form-control" name="category_id" id="category_id">
                         <option value="">Select Category</option>
                         @foreach($categories as $category)
                         <option value="{{$category->id}}" @if(old('category_id') == $category->id) selected @endif>{{$category->name}}</option>
                         @endforeach                       
                     </select>
                 </div>
             </div>

                    
                    <div class="form-group{{ $errors->has('percentage') ? ' has-error' : '' }}">
                        <label for="percentage" class="col-md-2 control-label">{!! $mend_sign !!}Percentage</label>
                        <div class="col-md-6">
                            <div class="input-icon">
                                <i class="fa fa-map-marker"></i>
                                <input type="text" class="form-control" name="percentage" id="percentage" placeholder="Enter Percentage"  value="{{ old('percentage') }}">
                                @if ($errors->has('percentage'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('percentage') }}</strong>
                                    </span>
                                @endif 
                            </div>
                        </div>
                    </div> 
                    
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

$(document).ready(function() {
    $("#frmFranchisePrice").validate({
        rules: {
            franchise_id:{
                required:true,
            },
            category_id:{
                required:true,
            },
            percentage:{
                required:true,
            },
            

        },
        messages: {
            franchise_id:{
                required:"@lang('validation.required',['attribute'=>'franchise_id'])",
            },
            category_id:{
                required:"@lang('validation.required',['attribute'=>'category_id'])",
            },
             percentage:{
                required:"@lang('validation.required',['attribute'=>'percentage'])",
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

    $("#frmFranchisePrice").submit(function(){
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