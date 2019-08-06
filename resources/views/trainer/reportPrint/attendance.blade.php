@extends('trainerLayouts.master')

@section('title')
Attendance
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/datatables/datatables.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/daterangepicker/daterangepicker-bs3.css') }}">
@endsection

@section('content')

@section('breadcrumb')
<li>

    <a href="{{ url('trainer/attendance') }}">Attendance</a>
</li>
@endsection

@section('pageheading')
Attendance - Print Preview
@endsection

<div class="row">
    <div class="col-md-12">
        @include('trainerLayouts.flash-message')

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
                            <th class="col-sm-5">Name</th>
                            <th class="col-sm-5">Package Name</th>
                            <th class="text-center col-sm-2">Date of Attend</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($Subscribers as $Subscriber)

                        <tr>
                            <td>{{ $Subscriber->name }}</td>
                            <td>{{ $Subscriber->package_name }}</td>
                             <td>{{ Carbon\Carbon::parse($Subscriber->date)->format('d/m/Y g:i:A') }}</td>
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
    var divToPrint = document.getElementById("printTable");
    var htmlToPrint = '<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">' +
            '<link rel="stylesheet" href="{{ asset('assets/css/print.css') }}">';
    htmlToPrint += divToPrint.outerHTML;
    newWin = window.open("");
    newWin.document.write(htmlToPrint);
    newWin.print();
    newWin.close();
    }
</script>  
@endsection