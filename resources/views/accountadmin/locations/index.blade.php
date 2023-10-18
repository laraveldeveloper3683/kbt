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
                        <!-- <h4 class="card-title">Locations Export</h4>
                        <h6 class="card-subtitle">Export locations to Copy, CSV, Excel, PDF & Print</h6> -->
                        <div class="table-responsive m-t-40">
                            <table id="example23" class="table table-striped"
                                cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Location Type</th>
                                        <th>Location Name</th>
                                        <th>Location Code</th>
                                        <th>Address</th>
                                        <th>City</th>
                                        <th>Zip</th>
                                        <th style="text-align:center;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                  @foreach($locations as $location)
                                    <tr>
                                        <td onclick="window.location='{{ route('accountadmin.locations.edit', ['id' => $location->pk_locations]) }}'">{{$location->location_types}}</td>
                                        <td onclick="window.location='{{ route('accountadmin.locations.edit', ['id' => $location->pk_locations]) }}'">{{$location->location_name}}</td>
                                        <td onclick="window.location='{{ route('accountadmin.locations.edit', ['id' => $location->pk_locations]) }}'">{{$location->location_code}}</td>
                                        <td onclick="window.location='{{ route('accountadmin.locations.edit', ['id' => $location->pk_locations]) }}'">{{$location->address}}</td>
                                        <td onclick="window.location='{{ route('accountadmin.locations.edit', ['id' => $location->pk_locations]) }}'">{{$location->city}}</td>
                                        <td onclick="window.location='{{ route('accountadmin.locations.edit', ['id' => $location->pk_locations]) }}'">{{$location->zip}}</td>
                                        <td style="text-align:center;">
                                         <a href="/accountadmin/locations/edit/{{$location->pk_locations}}"><button class="btn btn-danger text-white">Edit</button></a>
                                         <a href="javascript:" onclick="form_alert('locations-{{$location->pk_locations}}', '{{'want to delete '}}{{$location->location_name}} {{'Location?'}}')"><button class="btn btn-danger text-white">Delete</button></a>
                                         <form action="{{route('accountadmin.locations.delete',[$location->pk_locations])}}" method="get" id="locations-{{$location->pk_locations}}">
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
