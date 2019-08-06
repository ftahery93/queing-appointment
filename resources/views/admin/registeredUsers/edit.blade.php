@extends('layouts.master')

@section('title')
Registered Users
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
@endsection

@section('content')

@section('breadcrumb')
<li>

    <a href="{{ url('admin/registeredUsers')  }}">Registered Users</a>
</li>
@endsection

@section('pageheading')
Registered Users
@endsection
<form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"   action="{{ url('/admin/registeredUsers/'. $RegisteredUser->id)  }}" method="POST" id="form1" enctype="multipart/form-data">
    <input type="hidden" name="_method" value="patch">

    {{ csrf_field() }}

    <div class="row">
        <div class="col-md-12">
            @if(count($errors))
            @include('layouts.flash-message')
            @yield('form-error')
            @endif
            <div class="panel panel-primary" data-collapsed="0">

                <div class="panel-heading">


                    <div class="panel-options padding10">
                        <button type="submit" class="btn btn-green btn-icon">
                            Save
                            <i class="entypo-check"></i>
                        </button>
                        <a href="{{ url('admin/registeredUsers')  }}" class="margin-top0">
                            <button type="button" class="btn btn-red btn-icon">
                                Cancel
                                <i class="entypo-cancel"></i>
                            </button>
                        </a>

                    </div>
                </div>

                <div class="panel-body">


                    <div class="row">
                        <div class="form-group col-sm-12">

                            <div class="col-sm-6{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label for="name" class="col-sm-3 control-label">Name</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="name" autocomplete="off" value="{{ $RegisteredUser->name }}" name="name">
                                    @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </div>

                            <div class="col-sm-6{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-sm-3 control-label">Email ID</label>

                                <div class="col-sm-9">
                                    <input type="email" class="form-control" id="email" autocomplete="off" value="{{ $RegisteredUser->email }}" name="email"> 
                                    @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="row">
                        <div class="form-group col-sm-12">

                            <div class="col-sm-6{{ $errors->has('password') ? ' has-error' : '' }}">

                                <label for="password" class="col-sm-3 control-label">Password</label>

                                <div class="col-sm-9">
                                    <input type="password" class="form-control" id="password" autocomplete="off"  name="password">
                                    @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </div>

                            <div class="col-sm-6{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                <label for="password_confirmation" class="col-sm-3 control-label">Confirm Password</label>

                                <div class="col-sm-9">
                                    <input type="password" class="form-control" id="password_confirmation" autocomplete="off"  name="password_confirmation"> 
                                    @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="row">
                        <div class="form-group col-sm-12">

                            <div class="col-sm-6{{ $errors->has('mobile') ? ' has-error' : '' }}">
                                <label for="mobile" class="col-sm-3 control-label">Mobile</label>

                                <div class="col-sm-9">
                                    <input type="tel" class="form-control number_only" id="mobile" autocomplete="off" value="{{ $RegisteredUser->mobile }}" name="mobile">
                                    @if ($errors->has('mobile'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('mobile') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-sm-6{{ $errors->has('dob') ? ' has-error' : '' }}">
                                <label for="dob" class="col-sm-3 control-label">DOB</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control datetimepicker" id="dob" autocomplete="off"  value="{{ $RegisteredUser->dob }}" name="dob">
                                    @if ($errors->has('dob'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('dob') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="row">
                        <div class="form-group col-sm-12">

                            <div class="col-sm-6{{ $errors->has('area_id') ? ' has-error' : '' }}">
                                <label for="area_id" class="col-sm-3 control-label">Area</label>

                                <div class="col-sm-9">
                                    <select name="area_id" class="select2" data-allow-clear="true" >
                                        <option value="">--Select--</option>
                                        @foreach ($areas as $area)
                                        <option value="{{ $area->id }}" @if($RegisteredUser->area_id == $area->id) selected  @endif} >{{ $area->name_en }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('area_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('area_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-sm-6{{ $errors->has('gender_id') ? ' has-error' : '' }}">
                                <label for="gender_id" class="col-sm-3 control-label">Gender</label>

                                <div class="col-sm-9">
                                    <select name="gender_id" class="select2" data-allow-clear="true" >
                                        <option value="">--Select--</option>
                                        @foreach ($gender_types as $gender)
                                        <option value="{{ $gender->id }}" @if($RegisteredUser->gender_id == $gender->id) selected  @endif >{{ $gender->name_en }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('gender_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('gender_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="row">
                        <div class="form-group col-sm-12">

                            <div class="col-sm-6">
                                <label for="status" class="col-sm-3 control-label">Status</label>

                                <div class="col-sm-9">
                                    <select name="status" class="select2" data-allow-clear="true" id="status" >
                                        <option value="1" @if($RegisteredUser->status == 1) selected  @endif> Active</option>
                                        <option value="0" @if($RegisteredUser->status == 0) selected  @endif> Deactive</option>
                                    </select>
                                </div>
                            </div>

                            <?php /* ?> <div class="col-sm-6{{ $errors->has('username') ? ' has-error' : '' }}">
                              <label for="username" class="col-sm-3 control-label">Username</label>

                              <div class="col-sm-9">
                              <input type="text" class="form-control" id="username" autocomplete="off"  value="{{ $RegisteredUser->username }}" name="username">
                              @if ($errors->has('username'))
                              <span class="help-block">
                              <strong>{{ $errors->first('username') }}</strong>
                              </span>
                              @endif
                              </div>
                              </div> <?php */ ?>


                        </div>

                    </div>

                    <?php /* ?>
                      <div class="row">
                      <div class="form-group col-sm-12">

                      <div class="col-sm-6">

                      <label for="profile_image" class="col-sm-3 control-label">Profile Image</label>

                      <div class="col-sm-9">
                      <div class="fileinput fileinput-new" data-provides="fileinput">
                      <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;" data-trigger="fileinput">
                      <img src="{{ asset('assets/images/album-image-1.jpg') }}" alt="...">
                      </div>
                      <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px"></div>
                      <div>
                      <span class="btn btn-white btn-file">
                      <span class="fileinput-new">Select image</span>
                      <span class="fileinput-exists">Change</span>
                      <input type="file" name="profile_image" accept="image/*"  id="profile_image">
                      <input type="hidden" name="uploaded_image_removed" id="uploaded_image_removed" value="0">
                      </span>
                      <a href="#" class="btn btn-orange fileinput-exists" data-dismiss="fileinput">Remove</a>
                      </div>
                      </div>

                      </div>
                      </div>
                      @if($RegisteredUser->profile_image)
                      <div class="col-sm-6" id="uploaded_image">

                      <label for="profile_image" class="col-sm-3 control-label">Uploaded Profile Image</label>

                      <div class="col-sm-9">
                      <img src="{{ asset('registeredUsers_images/'.$RegisteredUser->profile_image) }}" alt="Uploaded Profile Image" style="max-width: 200px; max-height: 150px">

                      <div class="col-sm-12" style="margin-top:20px;">
                      <a href="javascript:void(0);" class="btn btn-orange fileinput-exists" data-dismiss="fileinput" id="remove_image">Remove</a>
                      </div>
                      </div>
                      </div>
                      @endif

                      </div><?php */ ?>

                </div> 

            </div>

        </div>

    </div>
</div>

</form>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('assets/js/daterangepicker/moment.js') }}"></script>
<script src="{{ asset('assets/js/datetimepicker/datetimepicker.js') }}"></script>
<script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.validate.js') }}"></script>
<!-- Imported scripts on this page -->
<script src="{{ asset('assets/js/fileinput.js') }}"></script>

<script type="text/javascript">

$(document).ready(function () {

    $.validator.addMethod("currency", function (value, element) {
        return this.optional(element) || /^[1-9]\d*(\.\d+)?$/.test(value);
    }, "Please specify a valid amount");
    var validator = $("#form1").validate({
        ignore: 'input[type=hidden], .select2-input, .select2-focusser',
        rules: {
            dob: "required",
            name: "required",
            email: "required",
            area_id: "required",
            gender_id: "required",
            mobile: {
                required: true,
                number: true,
                //minlength: 8,
                maxlength: 8
            },
        },
    });
//Remove Uploaded Image
    $('#remove_image').on('click', function (event) {
    $('profile_image').val('');
            $('#uploaded_image').hide('fast');
            $('#uploaded_image_removed').val('1');
            });
           
});

$('.number_only').keypress(function (e) {
                            return isNumbers(e, this);
                            });
                            function isNumbers(evt, element)
                            {
                            var charCode = (evt.which) ? evt.which : event.keyCode;
                            if (
                                    (charCode != 46 || $(element).val().indexOf('.') != - 1) && // “.�? CHECK DOT, AND ONLY ONE.
                                    (charCode > 57))
                                    return false;
                            return true;
                            }
</script>
<script>
    $(function () {
        /*-------Da            te-----------*/
        $('#dob').datepicker({
            format: 'dd/mm/yyyy',
            showTodayButton: true,
            sideBySide: true,
            showClose: true,
            showClear: true,
            keepOpen: true,
             endDate: "today",
            toolbarPlacement: 'bottom'
        });
    });
</script>
@endsection