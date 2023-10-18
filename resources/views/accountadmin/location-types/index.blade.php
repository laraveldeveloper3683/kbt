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
                                        <th>Location Type</th>
                                        <th style="text-align:center;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                  @foreach($locationtypes as $locationtype)
                                    <tr>
                                      <td onclick="window.location='{{ route('accountadmin.location-types.edit', ['id' => $locationtype->pk_location_types]) }}'">{{$locationtype->location_types}}</td>
                                        <td style="text-align:center;">
                                          <a href="/accountadmin/location-types/edit/{{$locationtype->pk_location_types}}"><button class="btn btn-danger text-white">Edit</button></a>
                                         <a href="/accountadmin/location-types/delete/{{$locationtype->pk_location_types}}"><button class="btn btn-danger text-white">Delete</button></a>
                                         <a href="javascript:" onclick="form_alert('location-types-{{$locationtype->pk_location_types}}', '{{'want to delete '}}{{$locationtype->location_types}} {{'Location type?'}}')"><button class="btn btn-danger text-white">Delete</button></a>
                                         <form action="{{route('accountadmin.location-types.delete',[$locationtype->pk_location_types])}}" method="get" id="location-types-{{$location->pk_locations}}">
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
