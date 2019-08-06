 <option value="">--Select Option Value--</option>
@foreach ($optionValue as $val)
<option value="{{ $val->id }}">{{ $val->name_en }}</option>
@endforeach