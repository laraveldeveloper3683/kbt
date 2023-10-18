<div class="col-md-3 align-self-center">
    <form method="post" action="/accountadmin/order-status" class="d-flex">
        @csrf
        <!-- <label for="role">Order Status</label>-->
        <select name="pk_order_status" id="pk_order_status" class="form-control" style="border-top-right-radius:0;border-bottom-right-radius:0;">
            <option value="">Filter Order by Status</option>
            @foreach ($order_status as $status)
                <option
                    value="{{ $status->pk_order_status }}">{{ ucfirst($status->order_status) }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-secondary" value="Go"
                style="margin-lef: 200px;margin-to: -70px;">Go
        </button>
    </form>
</div>
<div class="col-md-4 align-self-center p-0" style="margin-to: -18px;margin-lef: 80px;">
    <form method="POST" action="{{ route('accountadmin.payment.index.filter') }}">
        @csrf
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Search orders..." name="search"
                   value="{{ old('search', $search ?? '') }}" required>
            <div class="input-group-append">
                <button class="btn btn-secondary" type="submit">
                    <i class="fa fa-search"></i>
                </button>
            </div>
        </div>
    </form>
</div>
