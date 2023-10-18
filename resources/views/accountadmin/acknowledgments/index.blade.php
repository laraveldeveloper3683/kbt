@extends('layouts.backend_new')

@section('content')
    <!-- Page wrapper  -->
    <!-- ============================================================== -->
    <div class="page-wrapper">
        <!-- ============================================================== -->
        <!-- Container fluid  -->
        <!-- ============================================================== -->
        <div class="container-fluid">
            @if (Session::has('message'))
                <p class="alert alert-{{ Session::get('messageType') }}">{{ Session::get('message') }}</p>
            @endif
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
                                <table id="example23" class="display nowrap table table-hover table-striped border"
                                    cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Message Code</th>
                                            <th>Message Type</th>
                                            <th>Message</th>
                                            <th>Created By</th>
                                            <th>Updated By</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($acknowledgments as $Acknowledgment)
                                            <tr>
                                                <td onclick="goLink('{{ $Acknowledgment->pk_acknowledgments }}')">
                                                    {{ $Acknowledgment->message_code }}
                                                </td>
                                                <td onclick="goLink('{{ $Acknowledgment->pk_acknowledgments }}')">
                                                    {{ $Acknowledgment->message_type }}
                                                </td>
                                                <td onclick="goLink('{{ $Acknowledgment->pk_acknowledgments }}')">
                                                    {{ $Acknowledgment->message }}
                                                </td>
                                                <td onclick="goLink('{{ $Acknowledgment->pk_acknowledgments }}')">
                                                    {{ @$Acknowledgment->createdBy->first_name }}
                                                    {{ @$Acknowledgment->createdBy->last_name }}
                                                </td>
                                                <td onclick="goLink('{{ $Acknowledgment->pk_acknowledgments }}')">
                                                    {{ @$Acknowledgment->createdBy->first_name }}
                                                    {{ @$Acknowledgment->createdBy->last_name }}
                                                </td>
                                                <td style="text-align:center;">
                                                    <a
                                                        href="/accountadmin/acknowledgments/edit/{{ $Acknowledgment->pk_acknowledgments }}"><button
                                                            class="btn btn-danger text-white">Edit</button></a>
                                                    <a href="javascript:"
                                                        onclick="form_alert('acknowledgment-{{ $Acknowledgment->pk_acknowledgments }}', '{{ 'want to delete ' }}{{ $Acknowledgment->page }} {{ 'Page?' }}')"><button
                                                            class="btn btn-danger text-white">Delete</button></a>
                                                    <form
                                                        action="{{ route('accountadmin.acknowledgments.delete', [$Acknowledgment->pk_acknowledgments]) }}"
                                                        method="get"
                                                        id="acknowledgment-{{ $Acknowledgment->pk_acknowledgments }}">
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
<script>
    function goLink(id) {
        window.location.href = `/accountadmin.acknowledgments.edit/${id}`;
    }
</script>
