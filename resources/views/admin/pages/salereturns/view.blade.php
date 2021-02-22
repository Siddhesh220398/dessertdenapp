@extends('admin.layouts.app')

@section('breadcrumb')
    {!! Breadcrumbs::render('salereturns_details', $salereturn) !!}
@endsection
@section('content')
    <style type="text/css">
        #customers td, #customers th {
            border: 1px solid #ddd;
            padding: 8px;
            font-weight: bold;

        }

        #customers {
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        .child {
            border: 2px solid black;
            border-collapse: collapse;
            font-size: 12px;
        }
        .child tr{
            border-bottom:2pt solid black;
        }

    </style>


    <!-- BEGIN SAMPLE FORM PORTLET-->
    <div class="portlet light bordered">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa {{ $icon }} font-green"></i>
                <span class="caption-subject font-green bold uppercase">Invoice View</span>
            </div>
        </div>
        <div class="portlet-body ">
            <form method="Post" action="{{route('admin.invoices.print')}}">
                @csrf
                <div id="printTable">
                    <table style="width:100%;  font-weight: bold;">

                        <tr>
                            <td colspan="4">
                                <b style="font-size: 30px;  text-align: left;"><label>JEWIN FOODS</label></b>
                            </td>

                            <td rowspan="3" colspan="4">
                                <center><label>TUMMY TREAT</label></center>
                            </td>

                            <td rowspan="4" colspan="3" style="width: 120px;">
                                <center><img src="{{url('theme/images/logo.png')}}" style="width: 100px; height:20%;">
                                </center>
                            </td>

                        </tr>
                        <tr>
                            <td colspan="4">
                                <b style="font-size: 12px;">
                                    <label>Avadh Road, Off. Kalavad Road, Nr. Royal Enclave,
                                        Rajkot-360035</label></b>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b style="font-size: 12px;"><label>Mobile : +91 8000380038 <br/> Email :
                                        smile@dessertden.com</label></b>
                            </td>
                        </tr>
                    </table>

                    <table cellspacing="10px" id="customers"
                           class="child">

                        <tr>
                            <td style="text-align: left;" colspan="4">
                                <label>Debit Memo</label>
                            </td>
                            <td style="text-align: center;" colspan="3"><label>TAX INVOICE </label></td>
                            <td style="text-align: right;" colspan="4"><label>Original</label></td>
                        </tr>

                        <tr>
                            <td><label>Sr</label></td>
                            <td><label>Product Name </label></td>
                            <td><label>Order No</label></td>
                            <td><label>Quantity </label></td>
                            <td><label>HSN/SAC </label></td>
                            <td><label>Net Rate </label></td>
                            <td><label>Discount </label></td>
                            <td><label>GST %</label></td>
                            <td><label>CGST % </label></td>
                            <td><label>SGST %</label></td>
                            <td><label>Net Amount</label></td>
                        </tr>

                    </table>

                </div>
                <br/>
                <br/>
                <div class="form-group">
                    <button class=" btn btn-primary print" type="submit"> Print</button>
                </div>
            </form>

        </div>

        @endsection
        @push('scripts')
            <script type="text/javascript">
                // function printData() {
                //     var divToPrint = document.getElementById("printTable");
                //     newWin = window.open("");
                //     newWin.document.write(divToPrint.outerHTML);
                //     newWin.print();
                //     newWin.close();
                // }
                //
                // $('button').on('click', function () {
                //     printData();
                // })

                {{--$(document).on('click','.print',function () {--}}
                {{--    // alert($('.invoice_no').val());--}}
                {{--    $.ajax({--}}
                {{--        type: "Post",--}}
                {{--        url: "{{route('admin.invoices.print')}}",--}}
                {{--        data: {--}}
                {{--            '_token': $('input[name="_token"]').val(),--}}
                {{--            'invoice_no': $('.invoice_no').val(),--}}
                {{--        },--}}
                {{--        success: function (data) {--}}
                {{--            alert('success');--}}
                {{--        }--}}
                {{--    });--}}

                {{--});--}}
            </script>
    @endpush
