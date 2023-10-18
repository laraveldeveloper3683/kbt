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
                        <!-- <h4 class="card-title">Customers</h4> -->
                        <!-- <h6 class="card-subtitle">Export Customers to Copy, CSV, Excel, PDF & Print</h6> -->
                        <div class="table-responsive m-t-40">
                            <table id="example23"
                                class="display nowrap table table-hover table-striped border"
                                cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Host</th>
                                        <th>Port</th>
                                        <th>Encryption Type</th>
                                        <th style="text-align:center;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                  @foreach($emailAccounts as $emailAccount)
                                    <tr>
                                      <td onclick="window.location='{{ route('accountadmin.email-account.edit', ['id' => $emailAccount->pk_email_account]) }}'">{{$emailAccount->host}}</td>
                                      <td onclick="window.location='{{ route('accountadmin.email-account.edit', ['id' => $emailAccount->pk_email_account]) }}'">{{$emailAccount->port}}</td>
                                      <td onclick="window.location='{{ route('accountadmin.email-account.edit', ['id' => $emailAccount->pk_email_account]) }}'">{{$emailAccount->encryption_type}}</td>
                                        <td style="text-align:center;">
                                         <a href="/accountadmin/email-account/edit/{{$emailAccount->pk_email_account}}"><button class="btn btn-danger text-white">Edit</button></a>
                                         <a href="javascript:" onclick="form_alert('email-account-{{$emailAccount->pk_email_account}}', '{{'want to delete '}}{{'Email Account?'}}')"><button class="btn btn-danger text-white">Delete</button></a>
                                         <form action="{{route('accountadmin.email-account.delete',[$emailAccount->pk_email_account])}}" method="get" id="email-account-{{$emailAccount->pk_email_account}}">
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
