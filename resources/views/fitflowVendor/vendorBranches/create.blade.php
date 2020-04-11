@extends('vendorLayouts.master')

@section('title')
Branches
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<!-- Imported styles on this page -->
<link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/polaris/polaris.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/datetimepicker/bootstrap-datetimepicker.min.css') }}">
<style>
    .ev_src_Gr{height:250px;}
</style>
@endsection

@section('content')

@section('breadcrumb')
<li>

    <a href="{{ url($configName.'/vendorBranches') }}">Branches</a>
</li>
@endsection

@section('pageheading')
Branches
@endsection
<form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"  method="POST" action="{{ url($configName.'/vendorBranches') }}" id="form1" enctype="multipart/form-data" name="form1">
    {{ method_field('POST') }}
    {{ csrf_field() }}

    <div class="row">
        <div class="col-md-12">
            @if(count($errors))
            @include('vendorLayouts.flash-message')
            @yield('form-error')
            @endif
            <div class="panel panel-primary" data-collapsed="0">

                <div class="panel-heading">


                    <div class="panel-options padding10">
                        <button type="submit" class="btn btn-green btn-icon">
                            Save
                            <i class="entypo-check"></i>
                        </button>
                       <a href="{{ url($configName.'/vendorBranches') }}" class="margin-top0">
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
                            <input type="hidden" name="vendor_id" class="form-control" autocomplete="off" value="{{ $vendor_id }}">
                            <?php /* ?>
                              <div class="col-sm-6{{ $errors->has('vendor_id') ? ' has-error' : '' }}">
                              <label for="vendor_id" class="col-sm-3 control-label">Vendors</label>
                              <div class="col-sm-9">
                              <select name="vendor_id" class="select2" data-allow-clear="true">
                              <option value="">--Select Vendor--</option>
                              @foreach ($vendors as $vendor)
                              <option value="{{ $vendor->id }}" {{ (collect(old('vendor_id'))->contains($vendor->id)) ? 'selected':'' }} >{{ $vendor->name }}</option>
                              @endforeach
                              </select>
                              @if ($errors->has('vendor_id'))
                              <span class="help-block">
                              <strong>{{ $errors->first('vendor_id') }}</strong>
                              </span>
                              @endif
                              </div>
                              </div>
                             * <?php */ ?>

                            <div class="col-sm-6{{ $errors->has('gender_type') ? ' has-error' : '' }}">
                                <label for="gender_type" class="col-sm-3 control-label">Member Type</label>
                                <div class="col-sm-9">
                                    <select name="gender_type[]" class="select2" data-allow-clear="true" multiple="multiple"  id="gender">
                                        
                                        @foreach ($gender_types as $gender_type)
                                        <option value="{{ $gender_type->id }}" {{ (collect(old('gender_type'))->contains($gender_type->id)) ? 'selected':'' }} >{{ $gender_type->name_en }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('gender_type'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('gender_type') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-sm-6{{ $errors->has('contact') ? ' has-error' : '' }}" style="margin-top:10px;">

                                <label for="contact" class="col-sm-3 control-label">Contact No.</label>

                                <div class="col-sm-9 after-add-more">
                                    <input type="tel" name="contact" class="form-control" autocomplete="off" value="{{ old('contact') }}">
                                    @if ($errors->has('contact'))

                                    <span class="help-block">
                                        <strong>{{ $errors->first('contact') }}</strong>
                                    </span>

                                    @endif
                                </div>
                            </div>


                        </div>

                    </div> 

                    <div class="row">
                        <div class="form-group col-sm-12">

                            <div class="col-sm-6{{ $errors->has('name_en') ? ' has-error' : '' }}">

                                <label for="name_en" class="col-sm-3 control-label">Name(EN)</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="name_en" autocomplete="off" value="{{ old('name_en') }}" name="name_en">
                                    @if ($errors->has('name_en'))

                                    <span class="help-block">
                                        <strong>{{ $errors->first('name_en') }}</strong>
                                    </span>

                                    @endif
                                </div>

                            </div>

                            <div class="col-sm-6{{ $errors->has('name_ar') ? ' has-error' : '' }}">
                                <label for="name_ar" class="col-sm-3 control-label">Name(AR)</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="name_ar" autocomplete="off" value="{{ old('name_ar') }}" name="name_ar"> 
                                    @if ($errors->has('name_ar'))

                                    <span class="help-block">
                                        <strong>{{ $errors->first('name_ar') }}</strong>
                                    </span>

                                    @endif
                                </div>

                            </div>

                        </div>

                    </div>

                    <?php /* ?>   
                     *               <div class="row">
                      <div class="form-group col-sm-12">

                      <div class="col-sm-6{{ $errors->has('contact_person_en') ? ' has-error' : '' }}">

                      <label for="contact_person_en" class="col-sm-3 control-label">Contact Person(EN)</label>

                      <div class="col-sm-9">
                      <input type="text" class="form-control" id="contact_person_en" autocomplete="off" value="{{ old('contact_person_en') }}" name="contact_person_en">
                      @if ($errors->has('contact_person_en'))

                      <span class="help-block">
                      <strong>{{ $errors->first('contact_person_en') }}</strong>
                      </span>

                      @endif
                      </div>

                      </div>

                      <div class="col-sm-6{{ $errors->has('contact_person_ar') ? ' has-error' : '' }}">
                      <label for="contact_person_ar" class="col-sm-3 control-label">Contact Person(AR)</label>

                      <div class="col-sm-9">
                      <input type="text" class="form-control" id="contact_person_ar" autocomplete="off" value="{{ old('contact_person_ar') }}" name="contact_person_ar">
                      @if ($errors->has('contact_person_ar'))

                      <span class="help-block">
                      <strong>{{ $errors->first('contact_person_ar') }}</strong>
                      </span>

                      @endif
                      </div>

                      </div>

                      </div>

                      </div>
                     * <?php */ ?>
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6{{ $errors->has('area') ? ' has-error' : '' }}">
                                <label for="area" class="col-sm-3 control-label">Area</label>
                                <div class="col-sm-9">
                                    <select name="area" class="select2" data-allow-clear="true">
                                        <option value="">--Select Area--</option>
                                        @foreach ($areas as $area)
                                        <option value="{{ $area->id }}" {{ (collect(old('area'))->contains($area->id)) ? 'selected':'' }} >{{ $area->name_en }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('area'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('area') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                             <div class="col-sm-6{{ $errors->has('amenities') ? ' has-error' : '' }}">
                                <label for="amenities" class="col-sm-3 control-label">Amenities</label>
                                <div class="col-sm-9">
                                    <select name="amenities[]" class="select2" data-allow-clear="true" multiple="multiple"  id="amenities">
                                       
                                        @foreach ($amenities as $amenity)
                                        <option value="{{ $amenity->id }}" {{ (collect(old('amenities'))->contains($amenity->id)) ? 'selected':'' }} >{{ $amenity->name_en }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('amenities'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('amenities') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6">
                                <div class="col12">
                                    <label for="shifting_hours" class="col-sm-3 control-label">Shift 1 Timing</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control datetimepicker" id="shift1_time1" autocomplete="off" name="shifting_hours[shift1][start]" value="{{ old('shifting_hours[shift1][start]') }}"  
                                               @if(old('shifting_hours[fullshift][time]')== 1) checked @endif   >                                   
                                    </div>
                                    <div class="col-sm-4 pull-right">
                                        <input type="text" class="form-control datetimepicker" id="shift1_time2" autocomplete="off"  name="shifting_hours[shift1][end]" value="{{ old('shifting_hours[shift1][end]') }}"
                                               @if(old('shifting_hours[fullshift][time]')== 1) checked @endif   >                                
                                    </div>
                                </div>
                                <div class="clear visible-xs"></div>
                               <div class="col12">
                                    <label for="shifting_hours" class="col-sm-3 control-label">Shift 2 Timing</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control datetimepicker" id="shift2_time1" autocomplete="off"  name="shifting_hours[shift2][start]" value="{{ old('shifting_hours[shift2][start]') }}" 
                                               @if(old('shifting_hours[fullshift][time]')== 1) checked @endif    >                                 
                                    </div>
                                    <div class="col-sm-4 pull-right">
                                        <input type="text" class="form-control datetimepicker" id="shift2_time2" autocomplete="off" name="shifting_hours[shift2][end]" value="{{ old('shifting_hours[shift2][start]') }}"
                                               @if(old('shifting_hours[fullshift][time]')== 1) checked @endif   >                                   
                                    </div>
                                </div>
                                <div class="col12">
                                    <label for="minimal-checkbox-1-14" class="col-sm-3 control-label">24 Hours</label>
                                    <div class="col-sm-4">
                                        <input tabindex="5" type="checkbox" class="icheck-14 col-sm-4" id="minimal-checkbox-1-14" value="1" @if(old('shifting_hours[fullshift][time]')== 1) checked @endif >                                   
                                               <input type="hidden" value="{{ old('shifting_hours[fullshift][time]') }}" name="shifting_hours[fullshift][time]" id="fullshift" />
                                    </div>
                                    <div class="col-sm-4 pull-right">                                                                     
                                    </div>
                                </div>
                                
                                 <div class="col12">
                                    <label for="minimal-checkbox-1-14" class="col-sm-3 control-label">Main Branch</label>
                                    <div class="col-sm-4">
                                        <input tabindex="5" type="checkbox" class="icheck-14 col-sm-4" id="minimal-checkbox-1-14" value="1" @if(old('main_branch_id')== 1) checked @endif name="main_branch_id" >
                                    </div>
                                    <div class="col-sm-4 pull-right">                                                                     
                                    </div>
                                </div>

                            </div>
                            <div class="col-sm-6{{ $errors->has('address') ? ' has-error' : '' }}">
                                <label for="address" class="col-sm-3 control-label">Address</label>
                                <div class="col-sm-9">
                                    <textarea  class="form-control resize" name="address" id="address" >{{ old('address') }}</textarea>
                                    @if ($errors->has('address'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('address') }}</strong>
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
                                        <option value="1" {{ (collect(old('status'))->contains(1)) ? 'selected':'' }}> Active</option>
                                        <option value="0" {{ (collect(old('status'))->contains(0)) ? 'selected':'' }}> Deactive</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row">

                        <div class="form-group col-sm-12">
                            <div class="col-sm-12">
                                <label for="geo_address" class="col-sm-1 control-label">Location</label>
                                <div class="col-sm-11">
                                    <div class=" col-sm-10">
                                        <input type="text"  name="geo_address"  class="form-control" placeholder="Enter your address" id="geo_address"  />
                                    </div>  
                                    <input type="button" value="Search"  onClick="showAddress();
                                        ga('send', 'event', 'form', 'submit', 'address'); return false"  class="btn btn-primary col-sm-2"/>
                                    <div class="col-sm-12"  style="margin-top:10px;">
                                        <div style="display:inline;flex-wrap:wrap;">
                                            <div align="center" id="map" class="ev_src_Gr"></div>
                                            <div style="display: flex; flex-wrap:wrap; flex-direction:column; justify-content:flex-start;">

                                                <input type="hidden" id="lat" name="latitude">
                                                <input type="hidden" id="lng" name="longitude">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>

                    <?php /* ?> 
                     *                 <div class="row">                        
                      <div class="col-sm-6" style="margin-top:10px;">

                      <label for="contact" class="col-sm-3 control-label">Contact No.</label>

                      <div class="col-sm-9 after-add-more">
                      <div class="row">
                      <div class="col-sm-7">
                      <input type="text" name="contact[]" class="form-control" autocomplete="off">
                      </div>
                      <div class="col-sm-5">
                      <button class="btn btn-success add-more" type="button"><i class="glyphicon glyphicon-plus"></i></button>
                      </div>
                      </div>
                      </div>
                      </div>

                      Copy Fields

                      <div class="copy hide">

                      <div class="row clone_add"  style="margin-top:10px">
                      <div class="col-sm-7">
                      <input type="text" name="contact[]" class="form-control" autocomplete="off">
                      </div>
                      <div class="col-sm-5">
                      <button class="btn btn-danger remove" type="button"><i class="glyphicon glyphicon-remove"></i></button>
                      </div>
                      </div>
                      </div>
                      </div>
                     * <?php */ ?>

                </div>

            </div>

        </div>
    </div>

</form>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('assets/js/daterangepicker/moment.js') }}"></script>
<script src="{{ asset('assets/js/datetimepicker/bootstrap-datetimepicker.js') }}"></script>
<script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.validate.js') }}"></script>
<!-- Imported scripts on this page -->
<script src="{{ asset('assets/js/icheck/icheck.min.js') }}"></script>
<!-- Imported scripts on this page -->
<script>
                                        (function(i, s, o, g, r, a, m){i['GoogleAnalyticsObject'] = r; i[r] = i[r] || function(){
                                        (i[r].q = i[r].q || []).push(arguments)}, i[r].l = 1 * new Date(); a = s.createElement(o),
                                                m = s.getElementsByTagName(o)[0]; a.async = 1; a.src = g; m.parentNode.insertBefore(a, m)
                                        })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');
                                        ga('create', 'UA-17663461-1', 'auto');
                                        ga('send', 'pageview');</script>
<script type="text/javascript">
    $(document).ready(function () {

    $('input.icheck-14').iCheck({
    checkboxClass: 'icheckbox_polaris',
            radioClass: 'iradio_polaris'
    });
    //Add value 0 for unlimited num_points
    $('#minimal-checkbox-1-14').on('ifChecked', function (event) {
    $('.datetimepicker').val('').attr('readonly', 'readonly');
    $('#fullshift').val(1);
    });
    $('#minimal-checkbox-1-14').on('ifUnchecked', function (event) {
    $('.datetimepicker').val('').removeAttr('readonly');
    $('#fullshift').val(0);
    });
    $.validator.addMethod("currency", function (value, element) {
    return this.optional(element) || /^[1-9]\d*(\.\d+)?$/.test(value);
    }, "Please specify a valid amount");
    var validator = $("#form1").validate({
    ignore: 'input[type=hidden], .select2-input, .select2-focusser',
            rules: {
            name_en: "required",
                    name_ar: "required",
                    vendor_id: "required",
                    gender_type: "required",
                    area: "required",
                    latitude: "required",
                    longitude: "required",
                    contact: {
                    required: true,
                            number: true,
                            minlength: 8,
                            maxlength: 12
                    },
            },
    });
    });</script>
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=AIzaSyBVTKLztwVOGDuo1qGsjHzdY7wXRcKbAVI"> </script>   
<script>
    function load() {

    if (GBrowserIsCompatible()) {
    var map = new GMap2(document.getElementById("map"));
    map.addControl(new GSmallMapControl());
    map.addControl(new GMapTypeControl());
    var center = new GLatLng({{ $lat_lng }});
    map.setCenter(center, 14);
    geocoder = new GClientGeocoder();
    var marker = new GMarker(center, {draggable: true});
    map.addOverlay(marker);
    document.getElementById("lat").value = center.lat().toFixed(5);
    document.getElementById("lng").value = center.lng().toFixed(5);
    GEvent.addListener(map, "dragstart", function() {
    document.getElementById("weather").innerHTML = "";
    document.getElementById("weatherbutton").innerHTML = "Show weather";
    });
    GEvent.addListener(marker, "dragend", function() {
    ga('send', 'event', 'map', 'drag/move', 'map');
    var point = marker.getPoint();
    map.panTo(point);
    document.getElementById("lat").value = point.lat().toFixed(5);
    document.getElementById("lng").value = point.lng().toFixed(5);
    });
    GEvent.addListener(marker, "dragstart", function() {
    document.getElementById("weather").innerHTML = "";
    document.getElementById("weatherbutton").innerHTML = "Show weather";
    });
    GEvent.addListener(map, "moveend", function() {
    ga('send', 'event', 'map', 'drag/move', 'map');
    map.clearOverlays();
    var center = map.getCenter();
    var marker = new GMarker(center, {draggable: true});
    map.addOverlay(marker);
    document.getElementById("lat").value = center.lat().toFixed(5);
    document.getElementById("lng").value = center.lng().toFixed(5);
    GEvent.addListener(marker, "dragend", function() {

    ga('send', 'event', 'map', 'drag/move', 'map');
    var point = marker.getPoint();
    map.panTo(point);
    document.getElementById("lat").value = point.lat().toFixed(5);
    document.getElementById("lng").value = point.lng().toFixed(5);
    });
    });
    }
    }

    function showAddress() {
    var address = $('#geo_address').val();
    var map = new GMap2(document.getElementById("map"));
    map.addControl(new GSmallMapControl());
    map.addControl(new GMapTypeControl());
    if (geocoder) {
    geocoder.getLatLng(
            address,
            function(point) {
            if (!point) {
            alert(address + "Not Found");
            } else {
            document.getElementById("lat").value = point.lat().toFixed(5);
            document.getElementById("lng").value = point.lng().toFixed(5);
            map.clearOverlays()
                    map.setCenter(point, 14);
            var marker = new GMarker(point, {draggable: true});
            map.addOverlay(marker);
            GEvent.addListener(marker, "dragend", function() {
            var pt = marker.getPoint();
            map.panTo(pt);
            document.getElementById("lat").value = pt.lat().toFixed(5);
            document.getElementById("lng").value = pt.lng().toFixed(5);
            });
            GEvent.addListener(map, "moveend", function() {
            map.clearOverlays();
            var center = map.getCenter();
            var marker = new GMarker(center, {draggable: true});
            map.addOverlay(marker);
            document.getElementById("lat").value = center.lat().toFixed(5);
            document.getElementById("lng").value = center.lng().toFixed(5);
            GEvent.addListener(marker, "dragend", function() {
            var pt = marker.getPoint();
            map.panTo(pt);
            document.getElementById("lat").value = pt.lat().toFixed(5);
            document.getElementById("lng").value = pt.lng().toFixed(5);
            });
            GEvent.addListener(marker, "dragstart", function() {
            console.log('dragstart');
            document.getElementById("weather").innerHTML = "";
            document.getElementById("weatherbutton").innerHTML = "Show weather";
            });
            });
            }
            }
    );
    }
    }

    if (window.attachEvent) {
    window.attachEvent('onload', load);
    } else {
    if (window.onload) {
    var curronload = window.onload;
    var newonload = function() {
    curronload();
    load();
    };
    window.onload = newonload;
    } else {
    window.onload = load;
    }
    }


</script> 
<script type="text/javascript">


    $(document).ready(function () {


    $(".add-more").click(function () {

    var html = $(".copy").html();
    $(".after-add-more").append(html);
    });
    $("body").on("click", ".remove", function () {

    $(this).parents(".clone_add").remove();
    });
    });</script>
<script>
    $(function () {
    /*-------Date-----------*/
    $('#shift1_time1, #shift1_time2, #shift2_time1, #shift2_time2').datetimepicker({
    format: 'hh:mm:A',
            toolbarPlacement: 'bottom'
    });
    });
    // On change trainer name
//                           $(document.body).on("change","#gender",function(){
//                            alert(this.value);
//                           });
</script>
@endsection