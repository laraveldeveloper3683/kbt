@extends('layouts.backend_new')

@section('content')
<div class="page-wrapper">
    <div class="container-fluid">
        @include('common.admin-panel-top-area-new')
        <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title" style="text-align:center;">{{ isset($vendortype) && ($vendortype->pk_vendor_type) ? 'Edit Vendor Type':'Create New Vendor Type'}}</h4>
                                <div class="tab-content br-n pn">
                                    <div id="navpills-1" class="tab-pane active">
                                        <div class="row">
                                          @if(Session::has('message'))
                                            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
                                          @endif
                                          <form style="margin-left:550px;" class="form-horizontal mt-4 " method="post" action="{{ isset($vendortype) && ($vendortype->pk_vendor_type) ? '/accountadmin/vendor-type/update':'/accountadmin/vendor-type/submit'}}">
                                            @csrf
                                            <div class="form-group">
                                                <label for="product_category">Vendor Type</label>
                                                <input type="text" name="vendor_type" class="form-control @error('vendor_type') is-invalid @enderror" value="{{ isset($vendortype) && ($vendortype->vendor_type) ?$vendortype->vendor_type: old('vendor_type')}}">
                                                @error('vendor_type')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="description">Description</label>
                                                <input type="text" name="description" class="form-control @error('description') is-invalid @enderror" value="{{ isset($vendortype) && ($vendortype->description) ?$vendortype->description: old('description')}}">
                                                @error('description')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Active</label>
                                                    <input type="radio" name="active"  value="1" checked="checked" class="form-check-input">
                                                    <label class="form-check-label" for="customRadio11" style="margin-left: 20px;">Yes</label>
                                                    <input type="radio" name="active" value="0" {{ isset($vendortype) && ($vendortype->active=="0")? "checked" : "" }}  class="form-check-input">
                                                    <label class="form-check-label" for="customRadio22" style="margin-left: 20px;">No</label>
                                            </div>
                                            <input type="hidden" name="pk_account" value="{{isset($pk_account) ? $pk_account : ''}}" >
                                            <input type="hidden" name="pk_vendor_type" value="{{ isset($vendortype) && ($vendortype->pk_vendor_type) ?$vendortype->pk_vendor_type : ''}}" >
                                            <a href="/accountadmin/vendor-type/back"><input class="btn btn-primary" type="button" value="Cancel"></a>
                                            <input class="btn btn-primary" type="submit" value="{{ isset($vendortype) && ($vendortype->pk_vendor_type)? "Update" : "Submit" }}">
                                          </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    </div>
</div>

@endsection
