<table class="table border_0">

                        <tbody>
                            @foreach ($attendances as $attendance)
                            <tr>
                                <td><b>Date</b>  {{ Carbon\Carbon::parse($attendance->date)->format('d/m/Y') }}</td>
                                <td></td>
                                 <td><b>Status</b>  {{ $attendance->status==1?'Attend'
                                     :'Not Attend' }}</td>
                            </tr>
                            <tr>
                               
                            </tr>
                            @endforeach
                        </tbody>
                    </table>