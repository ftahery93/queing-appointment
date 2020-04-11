@extends('layouts.master')

@section('title')
Favourites
@endsection


@section('content')

@section('breadcrumb')
<li>

    <a href="{{ url('admin/vendorFavourites') }}">Favourites</a>
</li>
@endsection

@section('pageheading')
Favourites - Print Preview
@endsection

<div class="row">
    <div class="col-md-12">
        @include('layouts.flash-message')

        <div class="panel panel-dark" data-collapsed="0">   

            <div class="panel-body  table-responsive" id="printTable">
               
                <button  type="button" class="btn btn-success btn-icon pull-right no-print" id="btn">
                    Print 
                    <i class="entypo-print"></i>
                </button>
                <div class="loading-image" style="position:relative;"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                     <br/>
                     <br/>
               
                <table class="table table-bordered datatable" id="table-4">
                    <thead>
                        <tr>
                            <th class="col-sm-6">Subscriber</th>
                            <th class="col-sm-6">Vendor</th>
                            <th class="col-sm-2 text-center">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($Favourites as $Favourite)

                        <tr>
                            <td>{{ $Favourite->subscriber }}</td>
                             <td>{{ $Favourite->vendor }}</td>
                            <td>{{ Carbon\Carbon::parse($Favourite->created_at)->format('d/m/Y') }}</td>
                        </tr>                                                                                                                                                  

                        @endforeach
                    </tbody>

                </table>
            </div>

        </div>

    </div>
</div>

@endsection
@section('scripts')

<script>
  $(document).ready(function() {
        $(window).load(function(){
          $('.loading-image').hide();
       });
    });  
    /*---On Print All Confirmation---*/
    $("#btn").click(function () {
    printData();
});
    /*------END----*/
   function printData()
{
   var divToPrint=document.getElementById("printTable");
   
 var htmlToPrint ='<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">'  +
         '<link rel="stylesheet" href="{{ asset('assets/css/print.css') }}">';
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
   newWin= window.open("");
   newWin.document.write(htmlToPrint);
   newWin.print();
   newWin.close();
}
</script> 
@endsection

