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
                                        <th>Template Name</th>
                                        <th>Email Account</th>
                                        <th>Subject</th>
                                        <th style="text-align:center;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                  @foreach($emailTemplates as $emailTemplate)
                                    <tr>
                                      <td onclick="window.location='{{ route('accountadmin.email-template.edit', ['id' => $emailTemplate->pk_email_template]) }}'">{{$emailTemplate->template_name}}</td>
                                      <td onclick="window.location='{{ route('accountadmin.email-template.edit', ['id' => $emailTemplate->pk_email_template]) }}'">{{$emailTemplate->user_name}}</td>
                                      <td onclick="window.location='{{ route('accountadmin.email-template.edit', ['id' => $emailTemplate->pk_email_template]) }}'">{{$emailTemplate->subject}}</td>
                                        <td style="text-align:center;">
                                         <a href="/accountadmin/email-template/edit/{{$emailTemplate->pk_email_template}}"><button class="btn btn-danger text-white">Edit</button></a>
                                         <a href="javascript:" onclick="form_alert('email-template-{{$emailTemplate->pk_email_template}}', '{{'want to delete '}}{{'Email Template?'}}')"><button class="btn btn-danger text-white">Delete</button></a>
                                         <form action="{{route('accountadmin.email-template.delete',[$emailTemplate->pk_email_template])}}" method="get" id="email-template-{{$emailTemplate->pk_email_template}}">
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
