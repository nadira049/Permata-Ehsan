@php
    use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance PDF</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 6px 8px; text-align: left; }
        th { background: #f0f0f0; }
        h2 { margin-bottom: 0; }
        .meta { margin-top: 0; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h2>Attendance Record</h2>
    <div class="meta">
        @if(!empty($isMonth))
            <strong>Month:</strong> {{ $month }}<br>
        @else
            <strong>Date:</strong> {{ $date }}<br>
        @endif
        <strong>Year:</strong> {{ $year }}
    </div>
    @if(!empty($isMonth))
        @php
            $dates = collect($attendances)->keys()->sort()->values();
        @endphp
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    @foreach($dates as $d)
                        <th>{{ $d }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
            @foreach($children as $i => $child)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $child->name }}</td>
                    @foreach($dates as $d)
                        @php
                            $a = $attendances[$d]->firstWhere('child_id', $child->id) ?? null;
                        @endphp
                        <td>
                            {{ $a->status ?? '-' }}<br>
                            @if(isset($a->time) && $a->time)
                                {{ \Carbon\Carbon::parse($a->time)->format('H:i:s') }}<br>
                            @endif
                            <small>{{ $a->comment ?? '' }}</small>
                        </td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Time</th>
                    <th>Comment</th>
                </tr>
            </thead>
            <tbody>
            @foreach($children as $i => $child)
                @php $a = $attendances[$child->id] ?? null; @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $child->name }}</td>
                    <td>{{ $a->status ?? '-' }}</td>
                    <td>
                        @if(isset($a->time) && $a->time)
                            {{ \Carbon\Carbon::parse($a->time)->format('H:i:s') }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $a->comment ?? '-' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</body>
</html> 