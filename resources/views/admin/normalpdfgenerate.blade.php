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

          

          <td colspan="2"><label>Staff Name :{{  $name }}</label> </td>

          <td><label>Order No :{{  $customeorder->order->order_no }}</label></td>



          <td><label>Date: {{  \Carbon\Carbon::parse($customeorder->created_at)->format('d-m-Y') }}</label></td>

          <td><label>Time: {{  \Carbon\Carbon::parse($customeorder->created_at)->format('H-i A') }}</label></td>



        </tr>

       

        <tr>

          <td colspan="3"><label>Customer Name :{{!empty($order->user_id ) ? $order->user()->value('first_name'). " " . $order->user()->value('last_name') : App\Models\Franchise::where('id',$order->franchises_id)->value('name')}}</label></td>

          <td colspan="2"><label>Mobile No: {{!empty($order->user_id ) ? $order->user()->value('mobile_no'): App\Models\Franchise::where('id',$order->franchises_id)->value('mobile_no')}}
           </label></td>

        </tr>

        <tr>

          <td colspan="2"><label>Flavour :{{ App\Models\Flavour::where('id',$customeorder->flavour_id)->value('name') }}</label></td>
          <td colspan="2"><label>Type :</label></td>

          <td colspan="2"><label>Weight :{{ $customeorder->weight }}Kgs</label></td>

        </tr>

        <tr>

          <td colspan="3" style="height:300px;">

           <label>Cake</label>

           <br/> 

           <center>



            <img src="{{ $image }}" alt="Image" class="img-thumbnail" style="width: 200px; height: 200px;" />

            </center>

          </td>

          <td colspan="2">

            <label>Product Image</label>

            <br/> 

            <center>

            <img src="{{ $imageidea }}" alt="Image" class="img-thumbnail" style="width: 200px; height: 200px;" />

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

          <td colspan="2"><label>Delivery Date {{ date('d-m-Y',strtotime($customeorder->order->delivery_date)) }}</label></td>

        </tr>

        <tr>

        

          <td colspan="3"><label>Make On Time {{ date('h:i A',strtotime($customeorder->created_at)) }}</label></td>

          <td colspan="2"><label>Delivery Time </label></td>

        </tr>

      </tbody>

    </table>

  </center>     

</body>

</html>

