<x-app-layout>

    <style>
        .dashboard_mini_title {
            font-size: 24pt !important;
            font-weight: bold !important;
        }

        .count_number {
            font-size: 20pt !important;
            font-weight: bold !important;
        }

        .search-container {
            position: relative;
            margin: 10px auto;
            display: inline-block;
            /* Ensures it's only as wide as the content inside */
        }

        .search-input {
            height: 45px;
            border-radius: 25px;
            padding: 10px 40px;
            border: 1px solid #ccc;
            width: 100%;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            border-color: #01ad9d;
            outline: none;
            box-shadow: 0 0 8px rgba(1, 173, 157, 0.5);
        }

        .search-icon {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            font-size: 20px;
            color: #aaa;
            pointer-events: none;
        }

        .search-container .clear-icon {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            font-size: 18px;
            color: #aaa;
            cursor: pointer;
            display: none;
        }

        .search-input:not(:placeholder-shown)+.clear-icon {
            display: inline;
        }

        .search-input::placeholder {
            color: #aaa;
            font-size: 14px;
        }
    </style>

    <div class="row">
        <div class="col-md-12 col-12 p-3">
            <span class="title_header">Dashboard Donator</span>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-12">
            <div class="row mx-0 justify-content-between">
                <div class="col-md-6 col-12 my-2">
                    <div class="card shadow">
                        <div class="card-body">
                            <div class="row mx-0 my-2">
                                <div class="col-md-3 col-3 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-money-bill-transfer" style="font-size:60pt !important;"></i>
                                </div>
                                <div class="col-md-4 col-4 d-flex align-items-center">
                                    <span class="dashboard_mini_title">Total<br>Transactions</span>
                                </div>
                                <div class="col-md-5 col-5 d-flex align-items-center justify-content-center">
                                    <span class="count_number">{{ $invoiceCount ? $invoiceCount : '0' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-12 my-2">
                    <div class="card shadow">
                        <div class="card-body">
                            <div class="row mx-0 my-2">
                                <div class="col-md-3 col-3 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-hand-holding-dollar" style="font-size:60pt !important;"></i>
                                </div>
                                <div class="col-md-4 col-4 d-flex align-items-center">
                                    <span class="dashboard_mini_title">Total<br>Donations</span>
                                </div>
                                <div class="col-md-5 col-5 d-flex align-items-center justify-content-center">
                                    <span class="count_number">RM
                                        {{ $totalDonation ? number_format($totalDonation, 2) : '0.00' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center mx-0 my-3">
        <div class="col-md-12 col-12">
            <div class="card shadow">
                <div class="card-body">
                    <div class="row mx-0 my-2">
                        <div class="col-md-12 col-12">
                            <div class="card">
                                <div class="card-header" style="background:#01ad9d !important; color:white !important;">
                                    <div class="row">
                                        <div class="col-auto d-flex align-items-center">
                                            <b>General Fund</b>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 col-12">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th width="3%" class="text-center">#</th>
                                                        <th>Fund Name</th>
                                                        <th class="text-center">Collected Amount (MYR)</th>
                                                        <th class="text-center">Status</th>
                                                        <th width="20%" class="text-center">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if (isset($g_fund) && !empty($g_fund))
                                                        <tr>
                                                            <td class="text-center">1</td>
                                                            <td>{{ $g_fund->name }}</td>
                                                            <td class="text-center">
                                                                {{ $g_fund->collected_amount ? 'RM ' . number_format($g_fund->collected_amount, 2) : '-' }}
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="badge bg-success">Active</span>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="btn-group">
                                                                    <a href="{{ route('general_funds.fund_details', $g_fund->id) }}"
                                                                        class="btn btn-info">View</a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @else
                                                        <tr>
                                                            <td class="text-center" colspan="5">No General Fund
                                                                account created.</td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mx-0 my-2">
                        <div class="col-md-12 col-12">
                            <div class="card">

                                <div class="card-header" style="background:#01ad9d !important; color:white !important;">
                                    <div class="row">

                                        <div class="col-auto d-flex align-items-center">
                                            <b>Fund List</b>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 col-12 text-end">
                                    <div class="col-md-12 col-12 text-end">
                                        <div class="search-container">
                                            <form action="{{ route('donator.funds_search') }}" method="GET"
                                                class="form-inline">
                                                @csrf
                                                <input type="search" id="search-bar" name="searchkey"
                                                    class="form-control search-input"
                                                    placeholder="Search funds or details...">
                                                {{-- <button type="submit" class="btn btn-primary">Search</button> --}}
                                                <i class="fas fa-search search-icon"></i>
                                                <i class="fas fa-times clear-icon" onclick="clearSearch()"></i>
                                            </form>
                                        </div>
                                    </div>
                                </div>


                                <table cl <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 col-12">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th width="3%" class="text-center">#</th>
                                                        <th>Picture / Image</th>
                                                        <th>Fund Name</th>
                                                        <th class="text-center">Collected Amount / Target Amount (MYR)
                                                        </th>
                                                        <th class="text-center">End Date</th>
                                                        <th class="text-center">Status</th>
                                                        <th width="10%" class="text-center">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if (isset($funds) && count($funds) > 0)
                                                        @foreach ($funds as $fund)
                                                            <tr>
                                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                                <td>
                                                                    <img src="{{ asset('fund_image/' . $fund->image) }}"
                                                                        alt=""
                                                                        style="width:150px !important; height:auto !important; border-radius:15px !important;">
                                                                </td>
                                                                <td>
                                                                    {{ $fund->name }}
                                                                    @if ($fund->status == 'terminated')
                                                                        (Transferred <i
                                                                            class="fa-solid fa-money-bill-transfer"></i>
                                                                        General Fund)
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">
                                                                    {{ $fund->invoice->isNotEmpty() ? 'RM ' . $fund->invoice->sum('amount') : '-' }}
                                                                    / RM {{ $fund->target_amount }}</td>
                                                                <td class="text-center">
                                                                    {{ (new DateTime($fund->end_date))->format('d/m/Y') }}
                                                                </td>
                                                                <td class="text-center">
                                                                    @if ($fund->status == 'active')
                                                                        <span class="badge bg-success">Active</span>
                                                                    @elseif($fund->status == 'terminated')
                                                                        <span class="badge bg-danger">Terminated</span>
                                                                    @else
                                                                        <span class="badge bg-warning">Ended</span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">
                                                                    <div class="btn-group">
                                                                        <a href="{{ route('funds.fund_details', $fund->id) }}"
                                                                            class="btn btn-info">View</a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="7" class="text-center">No Funds Recorded.
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    @if (isset($funds) && count($funds) > 0)
                                        <div class="row mt-3">
                                            <div class="col-md-12 col-12">
                                                {{ $funds->links() }}
                                            </div>
                                        </div>
                                    @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script>
        function clearSearch() {
            const searchBar = document.getElementById('search-bar');
            searchBar.value = '';
            searchBar.dispatchEvent(new Event('input')); // Trigger search update
        }
    </script>

</x-app-layout>
