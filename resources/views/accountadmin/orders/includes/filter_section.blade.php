<div class="col-md-2 align-self-center">
    <form method="post" action="/accountadmin/order-status">
        @csrf
        <!-- <label for="role">Order Status</label>-->
        <select name="pk_order_status" id="pk_order_status" class="form-control">
            <option value="">Filter Order by Status</option>
            @foreach ($order_status as $status)
                <option
                    value="{{ $status->pk_order_status }}">{{ ucfirst($status->order_status) }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-secondary" value="Go"
                style="margin-left: 200px;margin-top: -70px;">Go
        </button>
    </form>
</div>
<div class="col-md-4 align-self-center p-3" style="margin-top: -18px;margin-left: 80px;">
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
