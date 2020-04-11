<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title">Previous Details</h4>
            </div>
            <div class="modal-body">
                <div class="loading-image"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                <table class="table border_0">

              <tbody>
                     <tr>
                            <td><b>Total Seats</b></td>
                            <td>{{ $classList->num_seats }}</td>
                        </tr>
                        <tr>
                            <td><b>Gym Seats</b></td>
                            <td>{{ $classList->available_seats }}</td>
                        </tr>
                        <tr>
                            <td><b>{{ $appTitle->title }} Seats</b></td>
                            <td>{{ $classList->fitflow_seats }}</td>
                        </tr>
                        <tr>
                            <td><b>Price</b></td>
                            <td>{{ $classList->price }}</td>
                        </tr>
                        <tr>
                            <td><b>Commission(%)</b></td>
                            <td>{{ $classList->commission_perc  }}</td>
                        </tr>  
                    </tbody>
                </table>
            </div>
<div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <!--                    <button type="button" class="btn btn-primary">Save changes</button>-->
            </div>


