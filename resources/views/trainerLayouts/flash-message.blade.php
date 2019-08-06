@if(Session::has('message'))

<div class="alert alert-success alert-block">

	<button type="button" class="close" data-dismiss="alert">×</button>	

        <strong>{{ Session::get('message') }}</strong>

</div>

@endif


@if(Session::has('error'))

<div class="alert alert-danger alert-block">

	<button type="button" class="close" data-dismiss="alert">×</button>	

        <strong>{{ Session::get('error') }}</strong>

</div>

@endif


@if ($message = Session::get('warning'))

<div class="alert alert-warning alert-block">

	<button type="button" class="close" data-dismiss="alert">×</button>	

	<strong>{{ $message }}</strong>

</div>

@endif


@if ($message = Session::get('info'))

<div class="alert alert-info alert-block">

	<button type="button" class="close" data-dismiss="alert">×</button>	

	<strong>{{ $message }}</strong>

</div>

@endif


<?php /*?>@if ($errors->any())

<div class="alert alert-danger">

	<button type="button" class="close" data-dismiss="alert">×</button>	

	Please check the form below for errors

</div>

@endif<?php */?>

@section('form-error')
            <div class="alert alert-danger alert-block">

                <button type="button" class="close" data-dismiss="alert">×</button>	

                <strong>Whoops!</strong> There were some problems with your input.

                <br/>

                <ul>

                    @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>
             @endsection
     