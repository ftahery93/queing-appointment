@extends('vendorLayouts.master')

@section('title')
Products
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
<!-- Imported styles on this page -->
<link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/polaris/polaris.css') }}">
<link rel="stylesheet" href="{{ asset('assets/js/datetimepicker/bootstrap-datetimepicker.min.css') }}">
@endsection

@section('content')

@section('breadcrumb')
<li>

    <a href="{{ url($configM4.'/products') }}">Products</a>
</li>
@endsection

@section('pageheading')
Products
@endsection
<form role="form" class="form-horizontal form-groups-bordered" autocomplete="off"  method="POST" action="{{ url($configM4.'/products') }}" id="form1" enctype="multipart/form-data">
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
                        <a href="{{ url($configM4.'/products') }}" class="margin-top0">
                            <button type="button" class="btn btn-red btn-icon">
                                Cancel
                                <i class="entypo-cancel"></i>
                            </button>
                        </a>

                    </div>
                </div>

                <div class="panel-body">
                    <div class="col-md-12">

                        <ul class="nav nav-tabs bordered"><!-- available classes "bordered", "right-aligned" -->
                            <li class="active">
                                <a href="#general" data-toggle="tab">
                                    <span class="visible-xs"><i class="entypo-home"></i></span>
                                    <span class="hidden-xs">General</span>
                                </a>
                            </li>
                            <li>
                                <a href="#option" data-toggle="tab">
                                    <span class="visible-xs"><i class="entypo-tools"></i></span>
                                    <span class="hidden-xs">Option</span>
                                </a>
                            </li>
                            <li>
                                <a href="#discount" data-toggle="tab">
                                    <span class="visible-xs"><i class="entypo-cog"></i></span>
                                    <span class="hidden-xs">Discount</span>
                                </a>
                            </li>
                            <li>
                                <a href="#special" data-toggle="tab">
                                    <span class="visible-xs"><i class="entypo-cog"></i></span>
                                    <span class="hidden-xs">Special</span>
                                </a>
                            </li>

                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane active" id="general">

                                <div class="row">
                                    <div class="form-group col-sm-12">
                                        <div class="col-sm-6">
                                            <label for="parent_id" class="col-sm-3 control-label">Category</label>

                                            <div class="col-sm-9">
                                                <select name="parent_id" class="select2" data-allow-clear="true" id="parent_id" >
                                                    <option value="0" {{ (collect(old('parent_id'))->contains(0)) ? 'selected':0 }}>-- Main Category --</option>
                                                    @foreach($allSubCategories as $subCate)
                                                    <option value="{{ $subCate->id }}" {{ (collect(old('parent_id'))->contains($subCate->parent_id)) ? 'selected':'' }}> {{ ucfirst($subCate->name_en) }}</option>
                                                    @endforeach()
                                                </select>

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


                                <div class="row">
                                    <div class="form-group col-sm-12">
                                        <div class="col-sm-6{{ $errors->has('description_en') ? ' has-error' : '' }}">
                                            <label for="description_en" class="col-sm-3 control-label">Description(EN)</label>
                                            <div class="col-sm-9">
                                                <textarea  class="form-control resize" name="description_en" id="description_en" >{{ old('description_en') }}</textarea>
                                                @if ($errors->has('description_en'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('description_en') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-sm-6{{ $errors->has('description_ar') ? ' has-error' : '' }}">
                                            <label for="description_ar" class="col-sm-3 control-label">Description(AR)</label>
                                            <div class="col-sm-9">
                                                <textarea  class="form-control resize" name="description_ar" id="description_ar"  dir="rtl">{{ old('description_ar') }}</textarea>
                                                @if ($errors->has('description_ar'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('description_ar') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>


                                </div>

                                <div class="row">
                                    <div class="form-group col-sm-12">

                                        <div class="col-sm-6{{ $errors->has('price') ? ' has-error' : '' }}">
                                            <label for="price" class="col-sm-3 control-label">Price</label>

                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="price" autocomplete="off" value="{{ old('price') }}" name="price">
                                                @if ($errors->has('price'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('price') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-sm-6{{ $errors->has('quantity') ? ' has-error' : '' }}">
                                            <label for="quantity" class="col-sm-3 control-label">Quantity</label>

                                            <div class="col-sm-9">
                                                <input type="text" class="form-control number_only" id="quantity" autocomplete="off" value="{{ old('quantity') }}" name="quantity"> 
                                                @if ($errors->has('quantity'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('quantity') }}</strong>
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

                                        <div class="col-sm-6{{ $errors->has('sort_order') ? ' has-error' : '' }}">
                                            <label for="sort_order" class="col-sm-3 control-label">Sort Order</label>

                                            <div class="col-sm-9">
                                                <input type="text" class="form-control number_only" id="sort_order" autocomplete="off" value="{{ old('sort_order') }}" name="sort_order"> 
                                                @if ($errors->has('sort_order'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('sort_order') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>

                                    </div>

                                </div>

                                <div class="row">
                                    <div class="form-group col-sm-12">

                                        <div class="col-sm-6">

                                            <label for="image" class="col-sm-3 control-label">Image</label>

                                            <div class="col-sm-9">
                                                <div class="fileinput fileinput-new" data-provides="fileinput"  id="error_file">
                                                    <div class="fileinput-new thumbnail" style="{{ $product_WH }}" data-trigger="fileinput">
                                                        <img src="{{ asset('assets/images/album-image-1.jpg') }}" alt="...">
                                                    </div>
                                                    <div class="fileinput-preview fileinput-exists thumbnail" style="{{ $product_WH }}"></div>
                                                    <div>
                                                        <span class="btn btn-white btn-file">
                                                            <span class="fileinput-new">Select image</span>
                                                            <span class="fileinput-exists">Change</span>
                                                            <input type="file" name="image" accept="image/*">
                                                        </span>
                                                        <a href="#" class="btn btn-orange fileinput-exists" data-dismiss="fileinput">Remove</a>
                                                        <p style="margin-top:20px;" ><b> Image Size: {{ $product_size }} </b></p>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                    </div>

                                </div>

                            </div>
                            <div class="tab-pane" id="option">
                                <div class="row">                       

                                    <div class="col-sm-12">
                                        <table class="table table-bordered" id="myTable">
                                            <thead>
                                                <tr>
                                                    <th class="text-center col-sm-3">Option</th>
                                                    <th class="text-center col-sm-3">Option Value</th>
                                                    <th class="text-center col-sm-2">Quantity</th>
                                                    <th class="text-center col-sm-3">Price</th>
                                                    <th class="text-center col-sm-1"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="after-add-more" id="row_id_1">  
                                                    <td>
                                                        <div class="col-sm-12{{ $errors->has('options_id.0') ? ' has-error' : '' }}">
                                                            <select name="options_id[]" class="col-sm-12 select2_pad options_id"  data-allow-clear="true"  placeholder="select option">
                                                                <option value="0">-Select Option-</option>
                                                                @foreach ($options as $val)
                                                                <option value="{{ $val->id }}" {{ (collect(old('options_id.0'))->contains($val->id)) ? 'selected':'' }}> {{ $val->name_en }}</option>
                                                                @endforeach
                                                            </select>
                                                            @if ($errors->has('options_id.0'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('options_id.0') }}</strong>
                                                            </span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="col-sm-12{{ $errors->has('option_value_id.0') ? ' has-error' : '' }}">
                                                            <select name="option_value_id[]" class="col-sm-12 select2_pad option_value_id"  data-allow-clear="true" placeholder="select option value">

                                                            </select>
                                                            @if ($errors->has('option_value_id.0'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('option_value_id.0') }}</strong>
                                                            </span>
                                                            @endif
                                                        </div> 
                                                    </td>
                                                    <td>
                                                        <div class="col-sm-12{{ $errors->has('option_value_quantity.0') ? ' has-error' : '' }}">
                                                            <input type="text" name="option_value_quantity[]" class="form-control" autocomplete="off" placeholder="Quantity" value="{{ old('option_value_quantity.0') }}">
                                                            @if ($errors->has('option_value_quantity.0'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('option_value_quantity.0') }}</strong>
                                                            </span>
                                                            @endif
                                                        </div> 
                                                    </td>
                                                    <td>
                                                        <div class="col-sm-12">
                                                            <select name="option_value_price_prefix[]" class="col-sm-12 select2_pad" data-allow-clear="true" id="option_value_price_prefix" >
                                                                <option value="+" {{ (collect(old('option_value_price_prefix'))->contains('+')) ? 'selected':'' }}> +</option>
                                                                <option value="-" {{ (collect(old('option_value_price_prefix'))->contains('-')) ? 'selected':'' }}> -</option>
                                                            </select>                                               
                                                            @if ($errors->has('option_value_price_prefix.0'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('option_value_price_prefix.0') }}</strong>
                                                            </span>
                                                            @endif
                                                        </div>  
                                                        <div class="col-sm-12">                                                 
                                                            <input type="text" name="option_value_price[]" class="form-control number_only" autocomplete="off" placeholder="Price" value="{{ old('option_value_price.0') }}">
                                                            @if ($errors->has('option_value_price.0'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('option_value_price.0') }}</strong>
                                                            </span>
                                                            @endif                                                
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <div class="col-sm-12">
                                                            <input type="hidden" name="total_item" id="total_item" value="1" disabled="disabled"/>
                                                            <button class="btn btn-success add-more" type="button"><i class="glyphicon glyphicon-plus"></i></button>
                                                        </div>   
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>

                                    </div>

                                    <!-- // Copy Fields-->

                                    <div class="copy hide">
                                        <table>
                                            <tbody>
                                                <tr class="clone_add" id="row_id_0">  
                                                    <td>
                                                        <div class="col-sm-12{{ $errors->has('options_id.0') ? ' has-error' : '' }}">
                                                            <select name="options_id[]" class="col-sm-12 select2_pad options_id"  data-allow-clear="true"  placeholder="select option"  disabled>
                                                                <option value="0">-Select Option-</option>
                                                                @foreach ($options as $val)
                                                                <option value="{{ $val->id }}" {{ (collect(old('options_id.0'))->contains($val->id)) ? 'selected':'' }}> {{ $val->name_en }}</option>
                                                                @endforeach
                                                            </select>
                                                            @if ($errors->has('options_id.0'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('options_id.0') }}</strong>
                                                            </span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="col-sm-12{{ $errors->has('option_value_id.0') ? ' has-error' : '' }}">
                                                            <select name="option_value_id[]" class="col-sm-12 select2_pad option_value_id"  data-allow-clear="true" placeholder="select option value"   disabled>

                                                            </select>
                                                            @if ($errors->has('option_value_id.0'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('option_value_id.0') }}</strong>
                                                            </span>
                                                            @endif
                                                        </div>  
                                                    </td>
                                                    <td>
                                                        <div class="col-sm-12{{ $errors->has('option_value_quantity.0') ? ' has-error' : '' }}">
                                                            <input type="text" name="option_value_quantity[]" class="form-control" autocomplete="off" placeholder="Quantity" value="{{ old('option_value_quantity.0') }}"  disabled="disabled">
                                                            @if ($errors->has('option_value_quantity.0'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('option_value_quantity.0') }}</strong>
                                                            </span>
                                                            @endif
                                                        </div> 
                                                    </td>
                                                    <td>
                                                        <div class="col-sm-12">
                                                            <select name="option_value_price_prefix[]" class="col-sm-12 select2_pad" data-allow-clear="true" id="option_value_price_prefix"  disabled="disabled" >
                                                                <option value="+" {{ (collect(old('option_value_price_prefix'))->contains('+')) ? 'selected':'' }}> +</option>
                                                                <option value="-" {{ (collect(old('option_value_price_prefix'))->contains('-')) ? 'selected':'' }}> -</option>
                                                            </select>                                               
                                                            @if ($errors->has('option_value_price_prefix.0'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('option_value_price_prefix.0') }}</strong>
                                                            </span>
                                                            @endif
                                                        </div>  
                                                        <div class="col-sm-12">                                                 
                                                            <input type="text" name="option_value_price[]" class="form-control number_only" autocomplete="off" placeholder="Price" value="{{ old('option_value_price.0') }}"  disabled="disabled">
                                                            @if ($errors->has('option_value_price.0'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('option_value_price.0') }}</strong>
                                                            </span>
                                                            @endif                                                
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <div class="col-sm-12">
                                                            <button class="btn btn-danger remove" type="button"><i class="glyphicon glyphicon-remove"></i></button>
                                                        </div>   
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="discount">
                                <div class="row">                       

                                    <div class="col-sm-12">
                                        <table class="table table-bordered" id="discount_myTable">
                                            <thead>
                                                <tr>                                                   
                                                    <th class="text-center col-sm-2">Quantity</th>
                                                    <th class="text-center col-sm-3">Price</th>
                                                    <th class="text-center col-sm-3">Date Start</th>
                                                    <th class="text-center col-sm-3">Date End</th>
                                                    <th class="text-center col-sm-1"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="discount_after-add-more" id="discount_row_id_1">                                                     
                                                    <td>
                                                        <div class="col-sm-12{{ $errors->has('discount_quantity.0') ? ' has-error' : '' }}">
                                                            <input type="text" name="discount_quantity[]" class="form-control" autocomplete="off" placeholder="Quantity" value="{{ old('discount_quantity.0') }}">
                                                            @if ($errors->has('discount_quantity.0'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('discount_quantity.0') }}</strong>
                                                            </span>
                                                            @endif
                                                        </div> 
                                                    </td>
                                                    <td>                                                        
                                                        <div class="col-sm-12">                                                 
                                                            <input type="text" name="discount_price[]" class="form-control number_only" autocomplete="off" placeholder="Price" value="{{ old('discount_price.0') }}">
                                                            @if ($errors->has('discount_price.0'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('discount_price.0') }}</strong>
                                                            </span>
                                                            @endif                                                
                                                        </div>
                                                    </td>                                                   
                                                    <td>                                                        
                                                        <div class="col-sm-12">  
                                                            <div class="input-group date">
                                                                <input type="text" name="discount_start_date[]" class="form-control number_only discount_start_date" autocomplete="off" placeholder="Start Date" value="{{ old('discount_start_date.0') }}">
                                                                <span class="input-group-btn"><button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button></span></div>
                                                            @if ($errors->has('discount_start_date.0'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('discount_start_date.0') }}</strong>
                                                            </span>
                                                            @endif                                                
                                                        </div>
                                                    </td>
                                                    <td>                                                        
                                                        <div class="col-sm-12"> 
                                                            <div class="input-group date">
                                                                <input type="text" name="discount_end_date[]" class="form-control number_only discount_end_date" autocomplete="off" placeholder="End Date" value="{{ old('discount_end_date.0') }}">
                                                                <span class="input-group-btn"><button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button></span></div>
                                                            @if ($errors->has('discount_end_date.0'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('discount_end_date.0') }}</strong>
                                                            </span>
                                                            @endif                                                
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <div class="col-sm-12">
                                                            <input type="hidden" name="discount_total_item" id="discount_total_item" value="1" disabled="disabled"/>
                                                            <button class="btn btn-success discount_add-more" type="button"><i class="glyphicon glyphicon-plus"></i></button>
                                                        </div>   
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>

                                    </div>

                                    <!-- // Copy Fields-->

                                    <div class="discount_copy hide">
                                        <table>
                                            <tbody>
                                                <tr class="discount_clone_add" id="discount_row_id_0">  
                                                    <td>
                                                        <div class="col-sm-12{{ $errors->has('discount_quantity.0') ? ' has-error' : '' }}">
                                                            <input type="text" name="discount_quantity[]" class="form-control" autocomplete="off" placeholder="Quantity" value="{{ old('discount_quantity.0') }}"  disabled="disabled">
                                                            @if ($errors->has('discount_quantity.0'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('discount_quantity.0') }}</strong>
                                                            </span>
                                                            @endif
                                                        </div> 
                                                    </td>
                                                    <td>                                                        
                                                        <div class="col-sm-12">                                                 
                                                            <input type="text" name="discount_price[]" class="form-control number_only" autocomplete="off" placeholder="Price" value="{{ old('discount_price.0') }}"  disabled="disabled">
                                                            @if ($errors->has('discount_price.0'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('discount_price.0') }}</strong>
                                                            </span>
                                                            @endif                                                
                                                        </div>
                                                    </td>                                                   
                                                    <td>                                                        
                                                        <div class="col-sm-12">  
                                                            <div class="input-group date">
                                                                <input type="text" name="discount_start_date[]" class="form-control number_only discount_start_date" autocomplete="off" placeholder="Start Date" value="{{ old('discount_start_date.0') }}"  disabled="disabled">
                                                                <span class="input-group-btn"><button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button></span></div>
                                                            @if ($errors->has('discount_start_date.0'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('discount_start_date.0') }}</strong>
                                                            </span>
                                                            @endif                                                
                                                        </div>
                                                    </td>
                                                    <td>                                                        
                                                        <div class="col-sm-12"> 
                                                            <div class="input-group date">
                                                                <input type="text" name="discount_end_date[]" class="form-control number_only discount_end_date" autocomplete="off" placeholder="End Date" value="{{ old('discount_end_date.0') }}"  disabled="disabled">
                                                                <span class="input-group-btn"><button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button></span></div>
                                                            @if ($errors->has('discount_end_date.0'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('discount_end_date.0') }}</strong>
                                                            </span>
                                                            @endif                                                
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <div class="col-sm-12">
                                                            <button class="btn btn-danger discount_remove" type="button"><i class="glyphicon glyphicon-remove"></i></button>
                                                        </div>   
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="special">
                                <div class="row">                       

                                    <div class="col-sm-12">
                                        <table class="table table-bordered" id="special_myTable">
                                            <thead>
                                                <tr> 
                                                    <th class="text-center col-sm-3">Price</th>
                                                    <th class="text-center col-sm-3">Date Start</th>
                                                    <th class="text-center col-sm-3">Date End</th>
                                                    <th class="text-center col-sm-1"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="special_after-add-more" id="special_row_id_1">                                                     

                                                    <td>                                                        
                                                        <div class="col-sm-12">                                                 
                                                            <input type="text" name="special_price[]" class="form-control number_only" autocomplete="off" placeholder="Price" value="{{ old('special_price.0') }}">
                                                            @if ($errors->has('special_price.0'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('special_price.0') }}</strong>
                                                            </span>
                                                            @endif                                                
                                                        </div>
                                                    </td>                                                   
                                                    <td>                                                        
                                                        <div class="col-sm-12">  
                                                            <div class="input-group date">
                                                                <input type="text" name="special_start_date[]" class="form-control number_only special_start_date" autocomplete="off" placeholder="Start Date" value="{{ old('special_start_date.0') }}">
                                                                <span class="input-group-btn"><button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button></span></div>
                                                            @if ($errors->has('special_start_date.0'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('special_start_date.0') }}</strong>
                                                            </span>
                                                            @endif                                                
                                                        </div>
                                                    </td>
                                                    <td>                                                        
                                                        <div class="col-sm-12"> 
                                                            <div class="input-group date">
                                                                <input type="text" name="special_end_date[]" class="form-control number_only special_end_date" autocomplete="off" placeholder="End Date" value="{{ old('special_end_date.0') }}">
                                                                <span class="input-group-btn"><button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button></span></div>
                                                            @if ($errors->has('special_end_date.0'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('special_end_date.0') }}</strong>
                                                            </span>
                                                            @endif                                                
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <div class="col-sm-12">
                                                            <input type="hidden" name="special_total_item" id="special_total_item" value="1" disabled="disabled"/>
                                                            <button class="btn btn-success special_add-more" type="button"><i class="glyphicon glyphicon-plus"></i></button>
                                                        </div>   
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>

                                    </div>

                                    <!-- // Copy Fields-->

                                    <div class="special_copy hide">
                                        <table>
                                            <tbody>
                                                <tr class="special_clone_add" id="special_row_id_0">                                                     
                                                    <td>                                                        
                                                        <div class="col-sm-12">                                                 
                                                            <input type="text" name="special_price[]" class="form-control number_only" autocomplete="off" placeholder="Price" value="{{ old('special_price.0') }}"  disabled="disabled">
                                                            @if ($errors->has('special_price.0'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('special_price.0') }}</strong>
                                                            </span>
                                                            @endif                                                
                                                        </div>
                                                    </td>                                                   
                                                    <td>                                                        
                                                        <div class="col-sm-12">  
                                                            <div class="input-group date">
                                                                <input type="text" name="special_start_date[]" class="form-control number_only special_start_date" autocomplete="off" placeholder="Start Date" value="{{ old('special_start_date.0') }}"  disabled="disabled">
                                                                <span class="input-group-btn"><button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button></span></div>
                                                            @if ($errors->has('special_start_date.0'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('special_start_date.0') }}</strong>
                                                            </span>
                                                            @endif                                                
                                                        </div>
                                                    </td>
                                                    <td>                                                        
                                                        <div class="col-sm-12"> 
                                                            <div class="input-group date">
                                                                <input type="text" name="special_end_date[]" class="form-control number_only special_end_date" autocomplete="off" placeholder="End Date" value="{{ old('special_end_date.0') }}"  disabled="disabled">
                                                                <span class="input-group-btn"><button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button></span></div>
                                                            @if ($errors->has('special_end_date.0'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('special_end_date.0') }}</strong>
                                                            </span>
                                                            @endif                                                
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <div class="col-sm-12">
                                                            <button class="btn btn-danger special_remove" type="button"><i class="glyphicon glyphicon-remove"></i></button>
                                                        </div>   
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
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
<script src="{{ asset('assets/js/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('assets/js/daterangepicker/moment.js') }}"></script>
<script src="{{ asset('assets/js/datetimepicker/datetimepicker.js') }}"></script>

<!-- Imported scripts on this page -->
<script src="{{ asset('assets/js/fileinput.js') }}"></script>
<script type="text/javascript">
$(document).ready(function () {

    var validator = $("#form1").validate({
        ignore: 'input[type=hidden], .select2-input, .select2-focusser',
        rules: {
            name_en: "required",
            name_ar: "required",
            parent_id: "required",
            image: "required",
            sort_order: {
                required: true,
                number: true
            },
            errorPlacement: function (error, element) {
                switch (element.attr("name")) {
                    case 'image':
                        error.insertAfter($("#error_file"));
                        break;
                    default:
                        error.insertAfter(element);
                }
            }
        }
    });

});
$('.number_only').keypress(function (e) {
    return isNumbers(e, this);
});
function isNumbers(evt, element)
{
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if (
            (charCode != 46 || $(element).val().indexOf('.') != -1) && // “.�? CHECK DOT, AND ONLY ONE.
            (charCode > 57))
        return false;
    return true;
}
</script>
<script type="text/javascript">


    $(document).ready(function () {
        //For option
        var count = $('#total_item').val();
        var trid = 0;
        $(".add-more").click(function () {
            count++;
            $('#total_item').val(count);
            $(".copy table tbody tr").removeAttr('id');
            var trid = $(".copy table tbody tr").attr("id", "row_id_" + count);
            if (trid != 0) {
                var html = $(".copy table tbody").html();
                $('#myTable tr:last').after(html);
                $(".after-add-more input,.after-add-more select").removeAttr('disabled');
            }

        });
        $("body").on("click", ".remove", function () {
            $(this).parents(".clone_add").remove();
            count--;
            $('#total_item').val(count);
        });

        //For Discount
        var discount_count = $('#discount_total_item').val();
        var discount_trid = 0;
        $(".discount_add-more").click(function () {
            discount_count++;
            $('#discount_total_item').val(discount_count);
            $(".discount_copy table tbody tr").removeAttr('id');
            var discount_trid = $(".discount_copy table tbody tr").attr("id", "discount_row_id_" + discount_count);
            if (discount_trid != 0) {
                var html = $(".discount_copy table tbody").html();
                $('#discount_myTable tr:last').after(html);
                $(".discount_after-add-more input,.discount_after-add-more select").removeAttr('disabled');
            }

            var discount_rowid = '#discount_row_id_' + discount_count

            $(discount_rowid + ' .discount_start_date').datepicker({
                format: 'dd/mm/yyyy',
                showTodayButton: true,
                sideBySide: true,
                showClose: true,
                showClear: true,
                keepOpen: true,
                toolbarPlacement: 'bottom'
            });
            $(discount_rowid + ' .discount_end_date').datepicker({
                format: 'dd/mm/yyyy',
                showTodayButton: true,
                sideBySide: true,
                showClose: true,
                showClear: true,
                keepOpen: true,
                toolbarPlacement: 'bottom'
            });
        });
        $("body").on("click", ".discount_remove", function () {
            $(this).parents(".discount_clone_add").remove();
            discount_count--;
            $('#discount_total_item').val(discount_count);
        });


        //For Special
        var special_count = $('#special_total_item').val();
        var special_trid = 0;
        $(".special_add-more").click(function () {
            special_count++;
            $('#special_total_item').val(special_count);
            $(".special_copy table tbody tr").removeAttr('id');
            var special_trid = $(".special_copy table tbody tr").attr("id", "special_row_id_" + discount_count);
            if (special_trid != 0) {
                var html = $(".special_copy table tbody").html();
                $('#special_myTable tr:last').after(html);
                $(".special_after-add-more input,.special_after-add-more select").removeAttr('disabled');
            }

            var special_rowid = '#special_row_id_' + discount_count

            $(special_rowid + ' .special_start_date').datepicker({
                format: 'dd/mm/yyyy',
                showTodayButton: true,
                sideBySide: true,
                showClose: true,
                showClear: true,
                keepOpen: true,
                toolbarPlacement: 'bottom'
            });
            $(special_rowid + ' .special_end_date').datepicker({
                format: 'dd/mm/yyyy',
                showTodayButton: true,
                sideBySide: true,
                showClose: true,
                showClear: true,
                keepOpen: true,
                toolbarPlacement: 'bottom'
            });
        });
        $("body").on("click", ".special_remove", function () {
            $(this).parents(".special_clone_add").remove();
            special_count--;
            $('#special_total_item').val(special_count);
        });

        //Image
        //For Special
//        var image_count = $('#image_total_item').val();
//        var image_trid = 0;
//        $(".image_add-more").click(function () {
//            special_count++;
//            $('#image_total_item').val(special_count);
//            $(".image_copy table tbody tr").removeAttr('id');
//            var image_trid = $(".image_copy table tbody tr").attr("id", "image_row_id_" + discount_count);
//            if (image_trid != 0) {
//                var html = $(".image_copy table tbody").html();
//                $('#image_myTable tr:last').after(html);
//                $(".image_after-add-more input").removeAttr('disabled');
//            }         
//        });
//        $("body").on("click", ".image_remove", function () {
//            $(this).parents(".image_clone_add").remove();
//            image_count--;
//            $('#image_total_item').val(image_count);
//        });


    });
</script>
<script>
    $(document).on('change', '.options_id', function () {
        var optionSelected1 = $("option:selected", this);
        var valueSelected1 = this.value;
        var trid = $(this).closest('tr').attr('id');
        $.ajax({
            type: "GET",
            async: true,
            "url": '{{ url("$configM4/products/getOptionValue") }}/' + valueSelected1,
            success: function (data) {
                $('#' + trid + ' .option_value_id').html(data.html);
            }
        });

    });
</script>
<script>

    $(function () {
        $('.discount_start_date').datepicker({
            format: 'dd/mm/yyyy',
            showTodayButton: true,
            sideBySide: true,
            showClose: true,
            showClear: true,
            keepOpen: true,
            toolbarPlacement: 'bottom'
        });
        $('.discount_end_date').datepicker({
            format: 'dd/mm/yyyy',
            showTodayButton: true,
            sideBySide: true,
            showClose: true,
            showClear: true,
            keepOpen: true,
            toolbarPlacement: 'bottom'
        });

        //Special
        $('.special_start_date').datepicker({
            format: 'dd/mm/yyyy',
            showTodayButton: true,
            sideBySide: true,
            showClose: true,
            showClear: true,
            keepOpen: true,
            toolbarPlacement: 'bottom'
        });
        $('.special_end_date').datepicker({
            format: 'dd/mm/yyyy',
            showTodayButton: true,
            sideBySide: true,
            showClose: true,
            showClear: true,
            keepOpen: true,
            toolbarPlacement: 'bottom'
        });

    });

</script>
@endsection