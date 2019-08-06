@foreach ($importedfiles as $importedfile)
<tr>
    <td>{{ ucfirst($importedfile->table_name) }}</td>
    <td class="text-center" style="font-size: 18px;"> <a href="{{ asset('importexceldata_tables/' . $importedfile->imported_file) }}" download data-toggle="tooltip" data-placement="top" title="Download Excel" data-original-title="Download Excel"><i class="fa fa-cloud-download" style="color:green;"></i></a></td>
    <td>{{ $importedfile->created_at->format('d/m/Y') }}</td>

</tr>
@endforeach
