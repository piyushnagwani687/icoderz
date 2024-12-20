@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h1 class="mb-4">Create Order</h1>
    <form id="order-form">
        @csrf
        <div class="mb-3">
            <label for="user_id" class="form-label">User:</label>
            <select name="user_id" class="form-select">
                <option value="">Select User</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
            <span class="text-danger error-text user_id"></span>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered" id="products-table">
                <thead class="table-dark">
                    <tr>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Amount</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="products-body">
                    <tr>
                        <td>
                            <input type="text" name="product_names[]" class="form-control">
                            <span class="text-danger error-text product_names_0"></span>
                        </td>
                        <td>
                            <input type="number" name="quantities[]" class="form-control qty" min="1">
                            <span class="text-danger error-text quantities_0"></span>
                        </td>
                        <td>
                            <input type="number" name="amounts[]" class="form-control amount" min="1">
                            <span class="text-danger error-text amounts_0"></span>
                        </td>
                        <td>
                            <input type="text" name="totals[]" class="form-control total" readonly>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-end fw-bold">Grand Total:</td>
                        <td><input type="text" name="grand_total" id="grand-total" class="form-control" readonly></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between mb-3">
            <button type="button" id="add-row" class="btn btn-primary">Add More</button>
        </div>

        <button type="submit" class="btn btn-success">Save</button>
    </form>
</div>

<script>
    var rowCount = 1;

    // Add Row
    $('#add-row').click(function(){
        $('#products-body tr:last').before(
            `<tr id="row-${rowCount}">
                <td>
                <input type="text" name="product_names[]" class="form-control product-name">
                <span class="text-danger error-text product_names_${rowCount}"></span>
            </td>
            <td>
                <input type="number" name="quantities[]" class="form-control qty" min="1">
                <span class="text-danger error-text quantities_${rowCount}"></span>
            </td>
            <td>
                <input type="number" name="amounts[]" class="form-control amount" min="1">
                <span class="text-danger error-text amounts_${rowCount}"></span>
            </td>
                <td><input type="text" name="totals[]" class="form-control total" readonly></td>
                <td><button type="button" class="btn btn-danger remove-row">Remove</button></td>
            </tr>`
        );
        rowCount++;
    });

    $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
        calculateSum();
    });

    $(document).on('input', '.qty, .amount', function () {
        let row = $(this).closest('tr');
        let qty = parseFloat(row.find('.qty').val()) || 0;
        let amount = parseFloat(row.find('.amount').val()) || 0;
        let total = (qty * amount).toFixed(2);
        row.find('.total').val(total);
        calculateSum();
    });

    function calculateSum() {
        let sum = 0;
        $('.total').each(function () {
            let value = parseFloat($(this).val()) || 0;
            sum += value;
        });
        $('#grand-total').val(sum.toFixed(2));
    }


    $('#order-form').on('submit', function (e) {
        e.preventDefault();
        $('.error-text').text('');

        $.ajax({
            url: "{{ route('orders.store') }}",
            method: "POST",
            data: $(this).serialize(),
            success: function (response) {
                if (response.status === 'success') {
                    window.location.href = response.redirect;
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    for (let key in errors) {
                        let errorKey = key.replace(/\./g, '_');
                        $('.error-text.' + errorKey).text(errors[key][0]);
                    }
                }
            }
        });
    });
</script>
@endsection
