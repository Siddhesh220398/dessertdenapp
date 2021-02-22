@extends('admin.layouts.app')

@section('breadcrumb')
{!! Breadcrumbs::render('users') !!}
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- Begin: life time stats -->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-green">
                    <span class="caption-subject bold uppercase">Filters</span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="form-group">
                        <label for="role" class="col-md-1">Role</label>
                        <div class="col-md-4">
                            <select id="role" class="form-control input-sm select2">
                                <option value="all">All</option>
                                <option value="host">Host</option>
                                <option value="guest">Guest</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="block-modal" tabindex="-1" role="block" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                <h4 class="modal-title">Block User</h4>
                            </div>
                            <div class="modal-body">
                                <form action="javascript:;" class="form-horizontal" role="form" method="POST">
                                    <input type="hidden" id="user_id" value="0">
                                    <div class="form-group">
                                        <label for="name" class="col-md-2 control-label">Type</label>
                                        <div class="col-md-6">
                                            <select class="form-control" id="block-type">
                                                <option value="permanent">Permanent Block</option>
                                                <option value="partial">Partial Block</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="partial-input" style="display: none;">
                                        <div class="form-group">
                                            <label for="name" class="col-md-2 control-label">Block Period</label>
                                            <div class="col-md-6">
                                                <input type="number" id="block-value" class="form-control" placeholder="Enter value of block time">
                                            </div>
                                            <div class="col-md-3">
                                                <select class="form-control" id="block-value-type">
                                                    <option value="min">Minutes</option>
                                                    <option value="hours">Hours</option>
                                                    <option value="days">Days</option>
                                                </select>
                                            </div>  
                                        </div>    
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                                <button type="button" class="btn green btn-save">Save changes</button>
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>
            </div>
        </div>
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-green">
                    <i class="fa {{ $icon }} font-green"></i>
                    <span class="caption-subject bold uppercase">{{$title}}</span>
                </div>
                <div class="tools"> 
                    
                </div>
                <div class="actions">
                    <a href="{{ route('admin.users.send_notification') }}" class="btn btn-circle btn-info"><i class="fa fa-location-arrow"></i> Send Notification</a>
                    @if (in_array('delete', $permissions))
                        <a href="{{ route('admin.users.destroy',0) }}" name="del_select" id="del_select" class="btn btn-circle btn-danger delete_all_link"><i class="fa fa-trash"></i> Delete Selected</a>
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
                { "title": "Name" ,"data": "name"},
                { "title": "Role" ,"data": "role"},
                { "title": "Email" ,"data": "email"},
                { "title": "Phone" ,"data": "phone"},
                @if (in_array('edit', $permissions))
                    { "title": "Block" ,"data": "active", searchble: false},
                    { "title": "Paid User" ,"data": "paid", searchble: false},
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
                "url": "{{route('admin.users.listing')}}", // ajax source
                "data": {
                    role: function(){
                        return $('#role').val();
                    }
                }
            },
            drawCallback: function( oSettings ) {
          		$('.make-switch').bootstrapSwitch();
          		$('.make-switch').bootstrapSwitch('onColor', 'success');
                $('.make-switch').bootstrapSwitch('offColor', 'danger');
        	},
            "dom": "<'row' <'col-md-12'>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable
        });

        $('.select2').select2();

        $('#role').change(function(){
            oTable.fnDraw();
        });

        $('#block-modal #block-type').change(function(){
            if($(this).val() == 'permanent') {
                $('#block-modal .partial-input').hide();
            } else {
                $('#block-modal .partial-input').show();
            }
        });

        $(document).on('click', 'a[href="#block-modal"]', function(){
            $('#block-modal #user_id').val($(this).data('id'));
        });

        $(document).on('click', '#block-modal button.btn-save', function(){
            $.ajax({
                url: "{{ route('admin.users.block') }}",
                type: 'POST',
                dataType: 'json',
                beforeSend:addOverlay,
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    user_id: function(){
                        return $('#block-modal #user_id').val();
                    },
                    block_type: function(){
                        return $('#block-modal #block-type').val();
                    },
                    block_value: function(){
                        return $('#block-modal #block-value').val();
                    },
                    block_value_type: function(){
                        return $('#block-modal #block-value-type').val();
                    },
                },
                success:function(r){
                    showMessage(r.status,r.message);
                    if(r.status == 200){
                        oTable.fnDraw();
                    }
                    $('#block-modal').modal('hide');
                },
                complete:removeOverlay
            });
        });
    });
</script>
@endpush
