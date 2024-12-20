@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Order List</h1>
    <a href="{{ route('orders.create')}}" class="btn btn-primary btn-sm">Create Order</a>
    <table id="orders-table" class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>User Name</th>
                <th>Total</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $order->user->name }}</td>
                <td>{{ $order->grand_total }}</td>
                <td>{{ $order->created_at->format('d,M Y, H:i:s') }}</td>
                <td><a href="{{ route('orders.show', $order->id) }}" class="btn btn-primary btn-sm">View</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div>

        {{ $orders->links('pagination::bootstrap-5') }}

    </div>
</div>

<script>
    $(document).ready(function () {
        $('#orders-table').DataTable({
            "paging": false,
            "ordering": true,
            "info": false,
            "searching": true,
        });
    });
</script>

@endsection
