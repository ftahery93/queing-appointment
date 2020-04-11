<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
    <h4 class="modal-title">Invoice</h4>
</div>
<div class="modal-body"  id="printTable">
    <div class="loading-image"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>

    <div id="rec-wrapper-inv">

        <div class="titleblock">                              

            <img src="{{ asset('assets/images/fitflow_logo_white.png') }}" style="width:80px;">

            <div class="pull-right"><h3>Receipt Voucher</h3></div>  
        </div>



        <div class="row-fluid" style="margin-top:10px;">     
            <table class="table table-bordered">

                <tbody>
                    <tr>

                        <td>Title:</td><td colspan="3">{{ $Invoice->title }}</td>                              

                    </tr>
                    <tr>

                        <td>Receipt No.:</td><td colspan="3">{{ $Invoice->receipt_num }}</td>                              

                    </tr>

                    <tr>

                        <td>Date:</td><td> {{ $Invoice->cdate }}</td>

                        <td>Amount (KD):</td><td>{{ $Invoice->price }}</td>

                    </tr>

                    <tr>

                        <td>Member Name:</td><td> {{ $Invoice->name }}</td>

                        <td colspan="2"></td>

                    </tr>

                    <tr>

                        <td>Fee (KD):</td><td> {{ $Invoice->amount }}</td>

                        <td colspan="2"></td>

                    </tr>

                    <tr>

                        <td>Being:</td><td colspan="3">{{ $Invoice->package_name }} {{ $Invoice->start_date }} - {{ $Invoice->end_date }}</td>

                    </tr>                              <tr>

                        <td>Receiver Sign:</td><td> </td>

                        <td>Accountant:</td><td></td>

                    </tr>                           </tbody></table>                  

        </div>



    </div>

</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
    <button  type="button" class="btn btn-success btn-icon pull-right no-print" id="btn" style="margin-left:10px;">
        Print 
        <i class="entypo-print"></i>
    </button>

</div>


<script>

    /*---On Print All Confirmation---*/
    $("#btn").click(function () {
    printData();
    });
    /*------END----*/
    function printData()
            {
            var divToPrint = document.getElementById("printTable");
            var htmlToPrint = '<link rel="stylesheet" href="{{ asset('assets / css / bootstrap.css') }}">' +
                    '<link rel="stylesheet" href="{{ asset('assets / css / print.css') }}">';
//         +
//        '<style type="text/css">' +
//        '.no-print{' +
//        'display: none !important;' +
//       // 'padding;0.5em;' +
//        '}' +
//        '</style>';

// var htmlToPrint = '' +
//        '<style type="text/css">' +
//        '.no-print{' +
//        'display: none !important;' +
//       // 'padding;0.5em;' +
//        '}' +
//        '</style>';

            htmlToPrint += divToPrint.outerHTML;
            newWin = window.open("");
            newWin.document.write(htmlToPrint);
            newWin.print();
            newWin.close();
            }
</script> 