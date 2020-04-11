@extends('layouts.master')

@section('title')
Users Group
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
@endsection

@section('content')

@section('breadcrumb')
<li>

    <a href="{{ url('admin/permissions')  }}">Users Group</a>
</li>
@endsection

@section('pageheading')
Users Group
@endsection
<form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"   action="{{ url('/admin/permissions/'. $Permission->id)  }}" method="POST" id="form1">
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
                        <a href="{{ url('admin/permissions')  }}" class="margin-top0">
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

                            <div class="col-sm-6{{ $errors->has('groupname') ? ' has-error' : '' }}">

                                <label for="groupname" class="col-sm-3 control-label">Group Name</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="groupname" autocomplete="off" value="{{ $Permission->groupname }}" name="groupname">
                                    @if ($errors->has('groupname'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('groupname') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <label for="status" class="col-sm-3 control-label">Status</label>

                                <div class="col-sm-9">
                                    <select name="status" class="select2" data-allow-clear="true" id="status" >
                                        <option value="1" @if($Permission->status == 1) selected  @endif > Active</option>
                                        <option value="0" @if($Permission->status == 0) selected  @endif > Deactive</option>
                                    </select>
                                </div>
                            </div>


                        </div>

                    </div>

                    <hr/>
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <label for="permissions" class="col-sm-1 control-label"><h3 class="text-success">Permissions</h3></label>
                        </div>   
                        <div class="col-sm-12">
                            <div class="panel panel-default" data-collapsed="0"><!-- to apply shadow add class "panel-shadow" -->
                                <!-- panel head -->
                                <div class="panel-heading">
                                    <div class="panel-title">General</div>
                                    <div class="panel-options">						
                                        <a href="#" data-rel="collapse"><i class="entypo-down-open"></i></a>

                                    </div>
                                </div>

                                <div class="panel-body no-padding">

                                    <div class="col-sm-12 margin10">
                                        @foreach ($admin_modules as $admin_module)
                                        @if ($admin_module->reports==0)
                                        <div class="row padding10">

                                            <label for="field-2" class="col-sm-2 control-label">{{ ucfirst($admin_module->module) }}</label>

                                            @if ($admin_module->view==1)
                                            <div class="col-sm-2">
                                                <label for="field-2" class="col-sm-4 control-label">View</label>
                                                <div class="make-switch">
                                                    <input type="checkbox"  @if ($collection->contains($admin_module->module_prefix.'-view')) checked  @endif name="permissions[]" value="{{ $admin_module->module_prefix }}-view">
                                                </div>
                                            </div>
                                            @endif

                                            @if ($admin_module->created==1)
                                            <div class="col-sm-2">
                                                <label for="field-2" class="col-sm-4 control-label">Create</label>
                                                <div class="make-switch">
                                                    <input type="checkbox"  @if ($collection->contains($admin_module->module_prefix.'-create')) checked  @endif name="permissions[]" value="{{ $admin_module->module_prefix }}-create">
                                                </div>
                                            </div>
                                            @endif


                                            @if ($admin_module->edit==1)
                                            <div class="col-sm-2"> 
                                                <label for="field-2" class="col-sm-4 control-label">Edit</label>
                                                <div class="make-switch">
                                                    <input type="checkbox" @if ($collection->contains($admin_module->module_prefix.'-edit')) checked  @endif name="permissions[]" value="{{ $admin_module->module_prefix }}-edit">
                                                </div>
                                            </div>
                                            @endif

                                            @if ($admin_module->deleted==1)
                                            <div class="col-sm-2">
                                                <label for="field-2" class="col-sm-4 control-label">Delete</label>
                                                <div class="make-switch">
                                                    <input type="checkbox" @if ($collection->contains($admin_module->module_prefix.'-delete')) checked  @endif name="permissions[]" value="{{ $admin_module->module_prefix }}-delete">
                                                </div>	
                                            </div>
                                            @endif

                                            @if ($admin_module->upload==1)
                                            <div class="col-sm-2">
                                                <label for="field-2" class="col-sm-4 control-label">Upload</label>
                                                <div class="make-switch">
                                                    <input type="checkbox" @if ($collection->contains($admin_module->module_prefix.'-upload')) checked  @endif name="permissions[]" value="{{ $admin_module->module_prefix }}-upload">
                                                </div>	
                                            </div>
                                            @endif

                                            @if ($admin_module->print==1)
                                            <div class="col-sm-2">
                                                <label for="field-2" class="col-sm-4 control-label">Print</label>
                                                <div class="make-switch">
                                                    <input type="checkbox" @if ($collection->contains($admin_module->module_prefix.'-print')) checked  @endif name="permissions[]" value="{{ $admin_module->module_prefix }}-print">
                                                </div>	
                                            </div>
                                            @endif

                                        </div>
                                        @endif
                                        @endforeach

                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="panel panel-default" data-collapsed="1"><!-- to apply shadow add class "panel-shadow" -->
                                <!-- panel head -->
                                <div class="panel-heading">
                                    <div class="panel-title">Reports</div>
                                    <div class="panel-options">						
                                        <a href="#" data-rel="collapse"><i class="entypo-down-open"></i></a>

                                    </div>
                                </div>

                                <div class="panel-body no-padding">

                                    <div class="col-sm-12 margin10">
                                        <!--  reports-->                                       
                                        @foreach ($admin_modules as $admin_module)
                                        @if ($admin_module->reports==1)
                                        <div class="row padding10">

                                            <label for="field-2" class="col-sm-2 control-label">{{ ucfirst($admin_module->module) }}</label>

                                            @if ($admin_module->view==1)
                                            <div class="col-sm-2">
                                                <label for="field-2" class="col-sm-4 control-label">View</label>
                                                <div class="make-switch">
                                                    <input type="checkbox"  @if ($collection->contains($admin_module->module_prefix.'-view')) checked="checked"  @endif name="permissions[]" value="{{ $admin_module->module_prefix }}-view">
                                                </div>
                                            </div>
                                            @endif

                                            @if ($admin_module->created==1)
                                            <div class="col-sm-2">
                                                <label for="field-2" class="col-sm-4 control-label">Create</label>
                                                <div class="make-switch">
                                                    <input type="checkbox"  @if ($collection->contains($admin_module->module_prefix.'-create')) checked  @endif name="permissions[]" value="{{ $admin_module->module_prefix }}-create">
                                                </div>
                                            </div>
                                            @endif


                                            @if ($admin_module->edit==1)
                                            <div class="col-sm-2"> 
                                                <label for="field-2" class="col-sm-4 control-label">Edit</label>
                                                <div class="make-switch">
                                                    <input type="checkbox" @if ($collection->contains($admin_module->module_prefix.'-edit')) checked  @endif name="permissions[]" value="{{ $admin_module->module_prefix }}-edit">
                                                </div>
                                            </div>
                                            @endif

                                            @if ($admin_module->deleted==1)
                                            <div class="col-sm-2">
                                                <label for="field-2" class="col-sm-4 control-label">Delete</label>
                                                <div class="make-switch">
                                                    <input type="checkbox" @if ($collection->contains($admin_module->module_prefix.'-delete')) checked  @endif name="permissions[]" value="{{ $admin_module->module_prefix }}-delete">
                                                </div>	
                                            </div>
                                            @endif

                                            @if ($admin_module->upload==1)
                                            <div class="col-sm-2">
                                                <label for="field-2" class="col-sm-4 control-label">Upload</label>
                                                <div class="make-switch">
                                                    <input type="checkbox" @if ($collection->contains($admin_module->module_prefix.'-upload')) checked  @endif name="permissions[]" value="{{ $admin_module->module_prefix }}-upload">
                                                </div>	
                                            </div>
                                            @endif

                                            @if ($admin_module->print==1)
                                            <div class="col-sm-2">
                                                <label for="field-2" class="col-sm-4 control-label">Print</label>
                                                <div class="make-switch">
                                                    <input type="checkbox" @if ($collection->contains($admin_module->module_prefix.'-print')) checked  @endif name="permissions[]" value="{{ $admin_module->module_prefix }}-print">
                                                </div>	
                                            </div>
                                            @endif

                                        </div>
                                        @endif
                                        @endforeach

                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>

</form>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.validate.js') }}"></script>
<!-- Imported scripts on this page -->
<script src="{{ asset('assets/js/bootstrap-switch.min.js') }}"></script>
<script type="text/javascript">
$(document).ready(function () {

    var validator = $("#form1").validate({
        ignore: 'input[type=hidden], .select2-input, .select2-focusser',
        rules: {
            groupname: "required"
        },

    });

});

</script>
@endsection