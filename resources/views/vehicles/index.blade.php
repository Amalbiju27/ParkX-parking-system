<th>Penalty</th>
<td>
    @if($vehicle->penalty > 0)
        <span style="color:red; font-weight:bold;">
            ₹{{ $vehicle->penalty }}
        </span>
    @else
        <span style="color:green;">
            No Penalty
        </span>
    @endif
</td>
