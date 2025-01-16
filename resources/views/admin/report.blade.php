<!DOCTYPE html>
<html>
<head>
    <title>TABEAM Transactions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <style>
        table {
            border-collapse: separate;
            border-spacing: 0 10px; /* Adds spacing between rows */
        }
        th, td {
            padding: 10px 15px; /* Adds padding inside cells */
        }
        th {
            text-align: center; /* Center align column headers */
        }
        tbody tr {
            background-color: #f9f9f9; /* Light background for rows */
        }
        tbody tr:hover {
            background-color: #e9ecef; /* Highlight row on hover */
        }
    </style>
</head>
<body>

    <h1>{{ $title }}</h1>
    <p>{{ $date }}</p>
    <br />

    @if(isset($transactions) && count($transactions) > 0)
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fund Name</th>
                    <th>Status</th>
                    <th>Donate By</th>
                    <th>Amount (RM)</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $index => $trans)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            @if($trans->donation_type == 'general' && $trans->general_fund)
                                {{ $trans->general_fund->name }}
                            @elseif($trans->fund)
                                {{ $trans->fund->name }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            @if($trans->fund)
                                @if($trans->fund->status == 'active')
                                    <span class="text-success">Active</span>
                                @elseif($trans->fund->status == 'ended')
                                    <span class="text-warning">Ended</span>
                                @else
                                    <span class="text-danger">Terminated</span>
                                @endif
                            @else
                                Active
                            @endif
                        </td>
                        <td>{{ $trans->donator->name ?? 'Unknown' }}</td>
                        <td>+ RM {{ number_format($trans->amount, 2) }}</td>
                        <td>{{ (new DateTime($trans->created_at))->format('d/m/Y') }}</td>
                        <td>{{ (new DateTime($trans->created_at))->format('h:i A') }}</td>
                        <td>{{ $trans->notes ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <center>No transaction records from funds.</center>
    @endif

</body>
</html>
