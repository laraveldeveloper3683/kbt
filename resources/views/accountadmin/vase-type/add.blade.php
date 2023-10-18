@extends('layouts.backend_new')

@section('content')
<div class="page-wrapper">
    <div class="container-fluid">
        @include('common.admin-panel-top-area-new')
        <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title" style="text-align: center;">{{ isset($VaseType) && ($VaseType->pk_vase_type) ? 'Edit Vase Type':'Create New Vase Type'}}</h4>
                                <div class="tab-content br-n pn">
                                    <div id="navpills-1" class="tab-pane active">
                                        <div class="row">
                                          @if(Session::has('message'))
                                            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
                                          @endif
                                          <form class="form-horizontal mt-4 "  style="margin-left: 550px;" method="post" action="/accountadmin/vase-types/submit">
                                            @csrf
                                            <div class="form-group">
                                                <label for="styles">Vase Type</label>
                                                <input type="text" name="vase_type" class="form-control @error('vase_type') is-invalid @enderror" value="{{ isset($VaseType) && ($VaseType->vase_type) ?$VaseType->vase_type: old('vase_type')}}">
                                                @error('vase_type')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="description">Description</label>
                                                <input type="text" name="description" class="form-control @error('description') is-invalid @enderror" value="{{ isset($VaseType) && ($VaseType->description) ?$VaseType->description: old('description')}}">
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
                                                    <input type="radio" name="active" value="0" {{ isset($VaseType) && ($VaseType->active=="0")? "checked" : "" }}  class="form-check-input" >
                                                    <label class="form-check-label" for="customRadio22" style="margin-left: 20px;">No</label>
                                            </div>
                                            <input type="hidden" name="pk_account" value="{{isset($pk_account) ? $pk_account : ''}}" >
                                            <input type="hidden" name="pk_vase_type" value="{{ isset($VaseType) && ($VaseType->pk_vase_type) ?$VaseType->pk_vase_type : ''}}" >
                                            <a href="/accountadmin/vase-types/back"><input class="btn btn-primary" type="button" value="Cancel"></a>
                                            <input class="btn btn-primary" type="submit" value="{{ isset($VaseType) && ($VaseType->pk_vase_type)? "Update" : "Submit" }}">
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
