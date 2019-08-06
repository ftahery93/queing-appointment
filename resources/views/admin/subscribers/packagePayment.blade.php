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
                            <td>{{ $payment->reference_id }}</td>
                        </tr>
                        <tr>
                            <td><b>Payment Type</b></td>
                            <td>{{ $payment->card_type }}</td>
                        </tr>
                        <tr>
                            <td><b>Amount (KD)</b></td>
                            <td>{{ $payment->amount }}</td>
                        </tr>
                        <tr>
                            <td><b>Date</b></td>
                            <td>{{ $payment->post_date }}</td>
                        </tr>
                        <tr>
                            <td><b>Payment Status</b></td>
                            <td>{{ $payment->result  }}</td>
                        </tr>  
                    </tbody>
                </table>
            </div>
<div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <!--                    <button type="button" class="btn btn-primary">Save changes</button>-->
            </div>


