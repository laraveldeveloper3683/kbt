@extends('layouts.backend_new')

@section('content')
<!-- Page wrapper  -->
<!-- ============================================================== -->
<div class="page-wrapper">
    <!-- ============================================================== -->
    <!-- Container fluid  -->
    <!-- ============================================================== -->
    <div class="container-fluid">
        <!-- ============================================================== -->
        <!-- Bread crumb and right sidebar toggle -->
        <!-- ============================================================== -->
        @include('common.admin-panel-top-area-new-button')
        <!-- ============================================================== -->
        <!-- End Bread crumb and right sidebar toggle -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Start Page Content -->
        <!-- ============================================================== -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- <h4 class="card-title">Product Category Export</h4>
                        <h6 class="card-subtitle">Export locations to Copy, CSV, Excel, PDF & Print</h6> -->
                        <div class="table-responsive m-t-40">
                            <table id="example23"
                                class="display nowrap table table-hover table-striped border"
                                cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Product Image</th>
                                        <th>Price</th>
                                        <th style="text-align:center;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                  @foreach($products as $product)
                                    <tr>
                                      <td onclick="window.location='{{ route('accountadmin.products.edit', ['id' => $product->pk_products]) }}'">{{$product->product}}</td>
                                      <td onclick="window.location='{{ route('accountadmin.products.edit', ['id' => $product->pk_products]) }}'">
                                      @foreach ($product->images as $image)
                                            <img src="/products/{{$image->path}}" alt="{{ $image->name }}" height="40" width="40">
                                      @endforeach
                                      </td>
                                      <td onclick="window.location='{{ route('accountadmin.products.edit', ['id' => $product->pk_products]) }}'">{{$product->price}} $</td>
                                        <td style="text-align:center;">
                                         <a href="/accountadmin/products/edit/{{$product->pk_products}}"><button class="btn btn-danger text-white">Edit</button></a>
                                         <a href="javascript:" onclick="form_alert('product-{{$product->pk_products}}', '{{'want to delete '}}{{$product->product}} {{'Product?'}}')"><button class="btn btn-danger text-white">Delete</button></a>
                                         <form action="{{route('accountadmin.products.delete',[$product->pk_products])}}" method="get" id="product-{{$product->pk_products}}">
                                         @csrf
                                         </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- End PAge Content -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Container fluid  -->
    <!-- ============================================================== -->
</div>
<!-- ============================================================== -->
<!-- End Page wrapper  -->
<!-- ============================================================== -->

@endsection
