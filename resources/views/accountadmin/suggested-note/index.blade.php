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
                                        <th>Event Type</th>
                                        <th>Suggested Note</th>
                                        <th style="text-align:center;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                  @foreach($SuggestedNotes as $SuggestedNote)
                                    <tr>
                                      <td onclick="window.location='{{ route('accountadmin.suggested-note.edit', ['id' => $SuggestedNote->pk_suggested_note]) }}'">{{$SuggestedNote->suggested_note}}</td>
                                      <td onclick="window.location='{{ route('accountadmin.suggested-note.edit', ['id' => $SuggestedNote->pk_suggested_note]) }}'">{{$SuggestedNote->suggested_note}}</td>
                                        <td style="text-align:center;">
                                         <a href="/accountadmin/suggested-note/edit/{{$SuggestedNote->pk_suggested_note}}"><button class="btn btn-danger text-white">Edit</button></a>
                                         <a href="javascript:" onclick="form_alert('suggested-note-{{$SuggestedNote->pk_suggested_note}}', '{{'want to delete '}}{{$SuggestedNote->suggested_note}} {{'User?'}}')"><button class="btn btn-danger text-white">Delete</button></a>
                                         <form action="{{route('accountadmin.suggested-note.delete',[$SuggestedNote->pk_suggested_note])}}" method="get" id="suggested-note-{{$SuggestedNote->pk_suggested_note}}">
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
