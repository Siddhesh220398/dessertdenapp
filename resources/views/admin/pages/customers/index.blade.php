@extends('admin.layouts.app')

@section('breadcrumb')
{!! Breadcrumbs::render('customers') !!}
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-green">
                    <i class="fa {{ $icon }} font-green"></i>
                    <span class="caption-subject bold uppercase">{{$title}}</span>
                </div>
                <div class="tools"> 
                    
                </div>
                <div class="actions">
                    @if (in_array('delete', $permissions))
                        <a href="{{ route('admin.customers.destroy',0) }}" name="del_select" id="del_select" class="btn btn-circle btn-danger delete_all_link"><i class="fa fa-trash"></i> Delete Selected</a>
                    @endif
                  
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-container">
                    <table class="table table-striped table-bordered table-hover table-user" id="table_DT">
                    </table>
                </div>
            </div>
        </div>
        <!-- End: life time stats -->
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    $(function(){
        var table = $('#table_DT');

        oTable = table.dataTable({
            "processing": true,
            "serverSide": true,
            "language": {
                "lengthMenu": "_MENU_ entries",
                "paginate": {
                    "previous": '<i class="fa fa-angle-left" ></i>',
                    "next": '<i class="fa fa-angle-right" ></i>'
                }
            },
            "columns": [
                @if (in_array('delete', $permissions))
                    { "title": "<input type='checkbox' class='all_select'>" ,"data": "checkbox","width":"3%",searchble: false, sortable:false},
                @endif
                { "title": "Firstname" ,"data": "first_name"},
                { "title": "Lastname" ,"data": "last_name"},
                { "title": "Email" ,"data": "email"},
                { "title": "Mobile no" ,"data": "mobile_no"},
                // { "title": "Profile" ,"data": "profile"},
               
             
                @if (in_array('edit', $permissions))
                    { "title": "Block" ,"data": "active", searchble: false},
                @endif
                @if (in_array('view', $permissions) || in_array('edit', $permissions) || in_array('delete', $permissions))
                    { "title": "Action" ,"data": "action", searchble: false, sortable:false }
                @endif
            ],
            responsive: false,
            "order": [
                @if (in_array('delete', $permissions))
                    [1, 'asc']
                @else
                    [0, 'asc']
                @endif
            ],
            "lengthMenu": [
                [10, 20, 50, 100],
                [10, 20, 50, 100]
            ],
            "pageLength": 10,
            "ajax": {
                "url": "{{route('admin.customers.listing')}}",
            },
            drawCallback: function( oSettings ) {
                $('.make-switch').bootstrapSwitch();
                $('.make-switch').bootstrapSwitch('onColor', 'success');
                $('.make-switch').bootstrapSwitch('offColor', 'danger');
            },
            "dom": "<'row' <'col-md-12'>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
        });
    });
</script>
@endpush
