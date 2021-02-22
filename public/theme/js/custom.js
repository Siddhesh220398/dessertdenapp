$(function(){
	/* Code to set sidebar li active */
    $('.page-sidebar-menu li').filter(function() {
        return $(this).hasClass('active');
    }).parent('ul').parent('li').addClass('active open');

    $('.page-sidebar-menu li').filter(function() {
        return $(this).hasClass('active');
    }).parent('ul').siblings('a').children('span.arrow').addClass('open');

    /**
     * Delete record from database
     */
    $(document).on('click', '.act-delete', function(e){
        e.preventDefault();
        var action = $(this).attr('href');
        bootbox.confirm('Are you sure! you want to delete this record?', function(res){
            if(res){
                $.ajax({
                    url: action,
                    type: 'DELETE',
                    dataType: 'json',
                    beforeSend:addOverlay,
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success:function(r){
                        showMessage(r.status,r.message);
                        if (typeof oTable.draw !== "undefined")
                        { 
                            oTable.draw();
                        }
                        else if (typeof oTable.fnDraw !== "undefined")
                        { 
                            oTable.fnDraw();
                        }
                    },
                    complete:removeOverlay
                });
            }
        });
        
    });

    $(document).on('switchChange.bootstrapSwitch','.status-switch', function(event, state) {
        var $this = $(this);
        var customAct = typeof $(this).data('getaction') != 'undefined' ? $(this).data('getaction') : '';
        var val = state ? 'y' : 'n';
        var url = $(this).data('url');
        var action =  customAct != '' ? customAct : 'change_status'; 
        
        $.ajax({
            url: url,
            type: 'PUT',
            dataType: 'json',
            beforeSend:addOverlay,
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                action:action, 
                value:val
            },
            success:function(r){
                showMessage(r.status,r.message);
                if(r.status != 200){
                    $this.prop("checked", !$this.prop("checked"));
                }
                else {
                    if(oTable.hasClass('table-user') && !$this.prop("checked")){
                        oTable.fnDraw();
                    }
                }
                removeOverlay();
            },
            complete:removeOverlay
        });
    });

    //start select all and delete records
    $(document).on('click', '.all_select', function () {
        if ($(this).hasClass('allChecked')) {
            if($(this).data('table') == '' || $(this).data('table') == undefined || $(this).data('table') == null)
                $('.dataTable tbody input[class="small-chk"]').prop('checked', false);
            else
                $('#' + $(this).data('table') + ' tbody input[class="small-chk"]').prop('checked', false);
                
        } else {
            if($(this).data('table') == '' || $(this).data('table') == undefined || $(this).data('table') == null)
                $('.dataTable tbody input[class="small-chk"]').prop('checked', true);
            else    
                $('#' + $(this).data('table') + ' tbody input[class="small-chk"]').prop('checked', true);
        }
        $(this).toggleClass('allChecked');
    });

    $(document).on('click', '.dataTable tbody input[class=small-chk]', function () {
        var numberOfChecked = $('.dataTable tbody input[class="small-chk"]:checked').length;
        var totalCheckboxes = $('.dataTable tbody input[class="small-chk"]').length;

        if(numberOfChecked > 0){
            if(numberOfChecked == totalCheckboxes){
                $('.all_select').prop('indeterminate',false);
                $('.all_select').prop('checked', true);
                $('.all_select').addClass('allChecked');
            }else{
                if ($('.all_select').hasClass('allChecked')) {
                    $('.all_select').removeClass('allChecked');
                }
                $('.all_select').prop('indeterminate',true);
            }
        }
        else{
            $('.all_select').prop('indeterminate',false);
            $('.all_select').prop('checked', false);
        }
    });

    $(document).on("click",".delete_all_link", function (e) {
        $(".delete_all_link").attr("disabled", "disabled");
        e.preventDefault();
        var url = $(this).attr('href');
        var searchIDs =[];
        $(".dataTable tbody input[class='small-chk']:checked").each(function() {
            searchIDs.push($(this).val());
        });
        if(searchIDs.length > 0){
            var ids = searchIDs.join();
            bootbox.confirm("Are you sure you want to delete selected records?", function(result) {
                if(result)
                {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        beforeSend: addOverlay,
                        dataType: 'json',
                        data: {
                            action:'delete_all',
                            ids:ids,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success:function(r){
                            showMessage(r.status,r.message);
                            if (typeof oTable.draw !== "undefined"){ 
                                oTable.draw();
                            }
                            else if (typeof oTable.fnDraw !== "undefined"){ 
                                oTable.fnDraw();
                            }
                            setTimeout(function(){ 
                              $('.all_select').prop('indeterminate',false);$('.all_select').prop('checked', false); 
                                if ($('.all_select').hasClass('allChecked')) {
                                $('.all_select').removeClass('allChecked');} }, 1000); 
                        },
                        complete:removeOverlay
                    }); 
                }
                $(".delete_all_link").removeAttr("disabled");
            });
        }else{
            bootbox.alert('please select at-least one record',function(){ 
                $('.all_select').prop('indeterminate',false);
                $(".delete_all_link").removeAttr("disabled"); 
            });
        }
    });
});

function getStatusText(code) {
    text = "";
    if (code !== undefined) {
        switch (code) {
            case 200:
                text = 'Success';
                break;
            case 404:
                text = 'Error';
                break; 
            case 403:
                text = 'Error';
                break; 
            case 500:
                text = 'Error';
                break;
            case "success":
                text = "Success";
                break;
            case "danger":
                text = 'Error';
                break;
            case "warning":
                text = 'Error';
                break;
            default:
                text = 'Error';
        }
    }
    return text;
}

function showMessage(type, text) {
    type = getStatusText(type);
    toastr[type.toLowerCase()](text);
}

function addOverlay() {
    var overlayContent = `<div id="overlayDocument">
                            <div class="loader">
                                <div class="bar b1"></div>
                                <div class="bar b2"></div>
                                <div class="bar b3"></div>
                                <div class="bar b4"></div>
                            </div>
                          </div>`;
    $(overlayContent).appendTo(document.body);
}

function removeOverlay(){
    $('#overlayDocument').remove();
}