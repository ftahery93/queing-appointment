@foreach ($OrderHistory as $History)   
<tr>
    <td class="text-left">{{ $History->created_at }}</td>
    <td class="text-left">{{ $History->comment }}</td>
    <td class="text-left">{{ $History->status }}</td>
</tr>
@endforeach