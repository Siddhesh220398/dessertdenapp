<!DOCTYPE html>

<html>

<head>

    <style type="text/css">

        table tr td {

            border: 1px solid black;

            font-size: 15px;

        }


    </style>


</head>

<body>

<center>

    <table class="table" style="border-collapse: collapse; width:100%;">

        <tbody>

        <tr>


            <td colspan="2"><label>Staff Name :{{  $name }}</label></td>

            <td><label>Order No :{{  $customeorder->order->order_no }}</label></td>


            <td><label>Date: {{  \Carbon\Carbon::parse($customeorder->created_at)->format('d-m-Y') }}</label></td>

            <td><label>Time: {{  \Carbon\Carbon::parse($customeorder->created_at)->format('H-i A') }}</label></td>


        </tr>


        <tr>

            <td colspan="3"><label>Name
                    :{{!empty($customeorder->order->user->first_name)? $customeorder->order->user->first_name :$customeorder->order->franchises->name}}</label>
            </td>

            <td colspan="2"><label>Mobile
                    No: {{!empty($customeorder->order->user->mobile_no)?$customeorder->order->user->mobile_no :$customeorder->order->franchises->mobile_no}}</label>
            </td>

        </tr>
        <tr>

            <td colspan="3"><label>Name
                    :{{!empty($customeorder->customer_name)? $customeorder->customer_name :''}}</label>
            </td>

            <td colspan="2"><label>Mobile
                    No: {{!empty($customeorder->customer_no)? $customeorder->customer_no :''}}</label>
            </td>

        </tr>




        <tr>
            <td ><label>Product :{{ App\Models\SubCategoryModel::where('id',$customeorder->sub_category_id)->value('name') }}</label></td>
            <td ><label>Flavour :{{ $customeorder->flavour->name }}</label></td>

            @php
                $priceRateName=[];
                                if(!empty($customeorder->type_rate)){
                                    $type_ids=json_decode( $customeorder->type_rate);
                                    $ar=[];
                                    foreach ($type_ids as $key => $value) {
                                        $priceRate=App\Models\PriceCategoryModel::where('id',$value)->first();					// array_push($priceRateName, $priceRate);
                                            array_push($ar,$priceRate->cat_name);

                                    }

                                }


                                if(!empty($customeorder->is_photo)){
                                $photoRate=App\Models\PriceCategoryModel::where('id',$customeorder->is_photo)->first();
                                }
            @endphp

            <td><label>Type :{{ !empty($ar)?implode(",",$ar).',':'-' }}
                    &nbsp; {{!empty($photoRate)?$photoRate->cat_name:''}}</label></td>

            <td colspan="2"><label>Weight :{{ $customeorder->weight }}Kgs</label></td>

        </tr>

        <tr>

            <td colspan="2" style="height:300px;">

                <label>Cake</label>

                <br/>

                <center>


                    <img src="{{ $image }}" alt="Image" class="img-thumbnail" style="width: 200px; height: 200px;"/>

                </center>

            </td>

            <td style="height:300px;">

                <label>Photo Cake</label>

                <br/>

                <center>


                    @if(!empty($customeorder->order->image))
                        <img src="{{ public_path('orders/'.$customeorder->order->image) }}" alt="Image"
                             class="img-thumbnail" style="width: 200px; height: 200px;"/>
                    @endif
                </center>

            </td>

            <td colspan="2">

                <label>Idea</label>

                <br/>

                <center>

                    <img src="{{ $imageidea }}" alt="Image" class="img-thumbnail" style="width: 200px; height: 200px;"/>

                </center>

            </td>

        </tr>


        <tr class="text-center" style="text-align: center;">

            <td colspan="5"><label>Instruction :{{ $customeorder->instruction }}</label></td>

        </tr>

        <tr class="text-center" style="text-align: center;">

            <td colspan="5"><label>Occassion :{{ $customeorder->theme }}</label></td>

        </tr>

        <tr class="text-center" style="text-align: center; ">

            <td colspan="5"><label>Message :{{ $customeorder->message_on_cake }}</label></td>

        </tr>

        <tr>

            <td colspan="3"><label>Make On Date {{ date('d-m-Y',strtotime($customeorder->created_at)) }}</label></td>

            <td colspan="2"><label>Delivery
                    Date {{ date('d-m-Y',strtotime($customeorder->order->delivery_date)) }}</label></td>

        </tr>

        <tr>


            <td colspan="3"><label>Make On Time :11 Pm</label></td>

            <td colspan="2"><label>Delivery Time </label></td>

        </tr>

        </tbody>

    </table>

</center>

</body>

</html>

