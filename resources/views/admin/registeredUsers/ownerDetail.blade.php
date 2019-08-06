<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title">Owner Details</h4>
            </div>
            <div class="modal-body">
                <div class="loading-image"><img src='{{ asset('assets/images/loader-1.gif') }}'></div>
                <table class="table border_0">

              <tbody>
                     <tr>
                            <td><b>Name</b></td>
                            <td>{{ ucfirst($ownerDetail->name) }}</td>
                        </tr>
                        <tr>
                            <td><b>Mobile</b></td>
                            <td>{{ $ownerDetail->mobile }}</td>
                        </tr>
                        
                    </tbody>
                </table>
            </div>
<div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <!--                    <button type="button" class="btn btn-primary">Save changes</button>-->
            </div>


