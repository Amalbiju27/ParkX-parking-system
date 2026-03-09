<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<table border="1" style="border-collapse: collapse; width: 100%; font-family: Arial, sans-serif;">
    <tr>
        <td colspan="6" style="font-size: 22px; font-weight: bold; text-align: center; background-color: #111111; color: #ffffff; height: 40px; vertical-align: middle;">
            PARKX SYSTEM EXPORT
        </td>
    </tr>
    <tr>
        <td colspan="6" style="font-size: 14px; font-weight: bold; text-align: center; background-color: #333333; color: #ffffff; height: 30px; vertical-align: middle;">
            EXECUTIVE FINANCIAL & OPERATIONAL REPORT
        </td>
    </tr>
    <tr><td colspan="6"></td></tr>
    <tr>
        <td colspan="2" style="font-weight: bold; background-color: #f8f9fa;">Generated On:</td>
        <td colspan="4">{{ date('F j, Y - g:i A') }}</td>
    </tr>
    <tr>
        <td colspan="2" style="font-weight: bold; background-color: #f8f9fa;">Exported By:</td>
        <td colspan="4">{{ auth()->user()->name ?? 'Administrator' }}</td>
    </tr>
    <tr><td colspan="6"></td></tr>
    <tr>
        <th style="font-weight: bold; background-color: #e5e7eb; height: 30px;">Parking Space</th>
        <th style="font-weight: bold; background-color: #e5e7eb;">Owner Name</th>
        <th style="font-weight: bold; background-color: #e5e7eb;">Contact Email</th>
        <th style="font-weight: bold; background-color: #e5e7eb;">Total Capacity</th>
        <th style="font-weight: bold; background-color: #e5e7eb;">Total Bookings</th>
        <th style="font-weight: bold; background-color: #e5e7eb;">Total Revenue (Rs)</th>
    </tr>
    @foreach($data['lots'] as $row)
    <tr>
        <td style="font-weight: bold; height: 25px;">{{ $row['name'] }}</td>
        <td>{{ $row['owner_name'] }}</td>
        <td>{{ $row['owner_email'] }}</td>
        <td>{{ $row['capacity'] }}</td>
        <td style="text-align: center;">{{ $row['bookings'] }}</td>
        <td style="text-align: right; color: #059669; font-weight: bold;">{{ number_format($row['revenue'], 2) }}</td>
    </tr>
    @endforeach
    <tr>
        <td colspan="4" style="font-weight: bold; text-align: right; background-color: #f3f4f6; height: 30px;">SYSTEM GRAND TOTALS:</td>
        <td style="font-weight: bold; text-align: center; background-color: #f3f4f6;">{{ $data['overallBookings'] }}</td>
        <td style="font-weight: bold; text-align: right; background-color: #f3f4f6; color: #111;">Rs. {{ number_format($data['overallRevenue'], 2) }}</td>
    </tr>
    <tr><td colspan="6"></td></tr>
    @if(isset($data['topPerformer']))
    <tr>
        <td colspan="6" style="font-size: 14px; font-weight: bold; background-color: #e5e7eb; height: 30px; text-align: center;">
            DETAILED SYSTEM REVIEW & INSIGHTS
        </td>
    </tr>
    <tr>
        <td colspan="2" style="font-weight: bold; background-color: #f8f9fa;">System Market Leader:</td>
        <td colspan="4" style="color: #059669; font-weight: bold;">{{ $data['topPerformer']['name'] }} (Rs. {{ number_format($data['topPerformer']['revenue'], 2) }})</td>
    </tr>
    <tr>
        <td colspan="2" style="font-weight: bold; background-color: #f8f9fa;">Revenue Distribution:</td>
        <td colspan="4">
            {{ $data['topPerformer']['name'] }} generated {{ $data['overallRevenue'] > 0 ? round(($data['topPerformer']['revenue'] / $data['overallRevenue']) * 100) : 0 }}% of total platform revenue.
        </td>
    </tr>
    @if(isset($data['runnerUp']))
    <tr>
        <td colspan="2" style="font-weight: bold; background-color: #f8f9fa;">Performance Gap:</td>
        <td colspan="4">
            The market leader generated Rs. {{ number_format($data['topPerformer']['revenue'] - $data['runnerUp']['revenue'], 2) }} more than the runner-up ({{ $data['runnerUp']['name'] }}).
        </td>
    </tr>
    @endif
    @endif
</table>
