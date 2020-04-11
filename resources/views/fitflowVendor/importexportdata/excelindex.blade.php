@foreach ($excelIndexes as $key=>$excelIndex)
<tr>
    <td>{{ ucfirst(str_replace("_"," ",$excelIndex))  }}</td>
    <td class="text-center" style="font-size: 18px;"> {{ $key }}</td>
</tr>
@endforeach
