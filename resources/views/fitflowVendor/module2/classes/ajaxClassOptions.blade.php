 <option value="">--Select--</option>
@foreach ($classes as $class)
<option value="{{ $class->id }}" data-hr="{{ $class->hours }}" @if($class_id!=0) selected @endif>{{ $class->name_en }}</option>
@endforeach