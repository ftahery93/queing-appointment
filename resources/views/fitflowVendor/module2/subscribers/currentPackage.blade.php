@if($currentPackage->detail_type == 0)
<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title">Current Package</h4>
            </div>
            <div class="modal-body">
                <div class="loading-image"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                <table class="table border_0">

              <tbody>
                     <tr>
                            <td><b>Package Name</b></td>
                            <td>{{ $currentPackage->name_en }}</td>
                        </tr>
                        <tr>
                            <td><b>Price (KD)</b></td>
                            <td>{{ $currentPackage->price }}</td>
                        </tr>
                        <tr>
                            <td><b>No. of Days</b></td>
                            <td>{{ $currentPackage->num_days }}</td>
                        </tr>
                        <tr>
                            <td><b>Start Date</b></td>
                            <td>{{ $currentPackage->start_date }}</td>
                        </tr>
                        <tr>
                            <td><b>End Date</b></td>
                            <td>{{ $currentPackage->end_date  }}</td>
                        </tr> 
                         <tr>
                            <td><b>No. of Classes</b></td>
                            <td>{{ $currentPackage->num_points  }}</td>
                        </tr> 
                    </tbody>
                </table>
            </div>
<div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <!--                    <button type="button" class="btn btn-primary">Save changes</button>-->
            </div>
@endif


@if($currentPackage->detail_type == 1) 
<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title">Payments Details</h4>
            </div>
            <div class="modal-body">
                <div class="loading-image"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                <table class="table border_0">

              <tbody>
                     <tr>
                            <td><b>Reference ID</b></td>
                            <td>{{ $currentPackage->reference_id }}</td>
                        </tr>
                        <tr>
                            <td><b>Payment Type</b></td>
                            <td>{{ $currentPackage->card_type }}</td>
                        </tr>
                        <tr>
                            <td><b>Amount (KD)</b></td>
                            <td>{{ $currentPackage->amount }}</td>
                        </tr>
                        <tr>
                            <td><b>Date</b></td>
                            <td>{{ $currentPackage->post_date }}</td>
                        </tr>
                        <tr>
                            <td><b>Payment Status</b></td>
                            <td>{{ $currentPackage->result  }}</td>
                        </tr>  
                    </tbody>
                </table>
            </div>
<div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <!--                    <button type="button" class="btn btn-primary">Save changes</button>-->
            </div>
@endif


