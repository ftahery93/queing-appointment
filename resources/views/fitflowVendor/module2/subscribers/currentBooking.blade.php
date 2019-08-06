
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
    <h4 class="modal-title">Bookings</h4>
</div>
<div class="modal-body">
    <div class="loading-image"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
    <div class="col-sm-6"><label>Total Classes:-  <span  class="badge orange" style="color:#fff;font-size:13px;margin-left:5px;">{{ $currentPackage->num_points }}</span></label></div>
    <div class="col-sm-6"><label>Total Booked:-   <span  class="badge orange" style="color:#fff;font-size:13px;margin-left:5px;">{{ $currentPackage->num_booked }}</span></label></div>
    <table class="table border_0">
   <thead>
                            <tr>
                                <th class="col-sm-2">Class Name</th>
                                <th class="col-sm-2 text-center">Start Time</th>
                                <th class="col-sm-2 text-center">End Time</th>
                                 <th class="col-sm-3 text-center">Schedule Date</th>                                 
                                <th class="col-sm-1 text-center">Created On</th>
                            </tr>
                        </thead>
        <tbody>
            @foreach ($currentBookings as $currentBooking)
            <tr>
                <td>{{ ucfirst($currentBooking->class_name) }}</td>
                <td  class="text-center">{{ Carbon\Carbon::parse($currentBooking->start)->format('h:m:A') }}</td>
                <td  class="text-center">{{ Carbon\Carbon::parse($currentBooking->end)->format('h:m:A') }}</td>                
                <td class="text-center">{{ Carbon\Carbon::parse($currentBooking->schedule_date)->format('d/m/Y') }}</td>
                 <td class="text-center">{{ Carbon\Carbon::parse($currentBooking->created_at)->format('d/m/Y') }} </td>

            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
    <!--                    <button type="button" class="btn btn-primary">Save changes</button>-->
</div>




