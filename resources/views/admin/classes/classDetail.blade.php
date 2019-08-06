<table class="table border_0">

                        <tbody>
                            <tr>
                                <td><b>Class Name</b></td>
                                <td>{{ $class_schedule->class_name }}</td>
                            </tr>
                            <tr>
                                <td><b>Schedule Date</b></td>
                                <td>{{ $class_schedule->schedule_date }}</td>
                            </tr>
                            <tr>
                                <td><b>Schedule</b></td>
                                <td>{{ $class_schedule->start }} - {{ $class_schedule->end }}</td>
                            </tr>
                              
                             @if($module_id==2)
                            <tr>
                                <td><b>Total Seats</b></td>
                                <td>{{ $class_schedule->gym_seats }}</td>
                            </tr>
                            <tr>
                                <td><b>Total Booked </b></td>
                                <td>{{ is_null($class_schedule->booked)?0:$class_schedule->booked }}</td>
                            </tr>
                            <tr>
                                <td><b>Total Remaining Seats</b></td>
                                <td>{{ $class_schedule->gym_seats-$class_schedule->booked }}</td>
                            </tr>
                            @endif
                            @if($module_id==3)
                             <tr>
                                <td><b>Total Seats</b></td>
                                <td>{{ $class_schedule->fitflow_seats }}</td>
                            </tr>
                             <tr>
                                <td><b>Total Booked </b></td>
                                <td>{{ is_null($class_schedule->app_booked)?0:$class_schedule->app_booked }}</td>
                            </tr>
                             <tr>
                                <td><b>Total Remaining Seats</b></td>
                                <td>{{ $class_schedule->fitflow_seats-$class_schedule->app_booked }}</td>
                            </tr>
                            @endif
                           
                        </tbody>
                    </table>