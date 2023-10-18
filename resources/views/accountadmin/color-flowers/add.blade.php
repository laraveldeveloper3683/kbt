@extends('layouts.backend_new')

@section('content')
<div class="page-wrapper">
    <div class="container-fluid">
        @include('common.admin-panel-top-area-new')
        <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title" style="text-align:center;">{{ isset($ColorFlower) && ($ColorFlower->pk_color_flower) ? 'Edit Color Flower':'Create New Color Flower'}}</h4>
                                <div class="tab-content br-n pn">
                                    <div id="navpills-1" class="tab-pane active">
                                        <div class="row">
                                          @if(Session::has('message'))
                                            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
                                          @endif
                                          <form class="form-horizontal mt-4 " method="post" action="/accountadmin/color-flowers/submit" style="margin-left: 550px;">
                                            @csrf
                                            <div class="form-group">
                                                <label for="styles">Color Flower</label>
                                                <input type="text" name="color_flower" class="form-control @error('color_flower') is-invalid @enderror" value="{{ isset($ColorFlower) && ($ColorFlower->color_flower) ?$ColorFlower->color_flower: old('color_flower')}}">
                                                @error('color_flower')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="description">Description</label>
                                                <input type="text" name="description" class="form-control @error('description') is-invalid @enderror" value="{{ isset($ColorFlower) && ($ColorFlower->description) ?$ColorFlower->description: old('description')}}">
                                                @error('description')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Active</label>
                                                    <input type="radio" name="active"  value="1" checked="checked" class="form-check-input" >
                                                    <label class="form-check-label" for="customRadio11" style="margin-left: 20px;">Yes</label>
                                                    <input type="radio" name="active" value="0" {{ isset($ColorFlower) && ($ColorFlower->active=="0")? "checked" : "" }}  class="form-check-input" >
                                                    <label class="form-check-label" for="customRadio22" style="margin-left: 20px;">No</label>
                                            </div>
                                            <input type="hidden" name="pk_account" value="{{isset($pk_account) ? $pk_account : ''}}" >
                                            <input type="hidden" name="pk_color_flower" value="{{ isset($ColorFlower) && ($ColorFlower->pk_color_flower) ?$ColorFlower->pk_color_flower : ''}}" >
                                            <a href="/accountadmin/color-flowers/back"><input class="btn btn-primary" type="button" value="Cancel"></a>
                                            <input class="btn btn-primary" type="submit" value="{{ isset($ColorFlower) && ($ColorFlower->pk_color_flower)? "Update" : "Submit" }}">
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
