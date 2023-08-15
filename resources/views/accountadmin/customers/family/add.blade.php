@extends('layouts.backend_new')

@section('content')
    <div class="page-wrapper">
        <div class="container-fluid">
            <div class="row page-titles">
                <div class="col-md-5 align-self-center">
                    <h4 class="text-themecolor">Dashboard</h4>
                </div>
                <div class="col-md-7 align-self-center text-end">
                    <div class="d-flex justify-content-end align-items-center">
                        <ol class="breadcrumb justify-content-end">
                            <li class="breadcrumb-item"><a href="/accountadmin">Home</a></li>
                            <li class="breadcrumb-item active">Adding Customer Family</li>
                        </ol>
                    </div>
                </div>
            </div>
              <div class="row">
                  <div class="col-md-12">
                      <div class="card">
                          <div class="card-body">
                              <h4 class="card-title">{{isset($family) && ($family->pk_customer_family)?'Edit':'Create'}} Family</h4>
                              <div class="tab-content br-n pn">
                                  <div id="navpills-1" class="tab-pane active">
                                      <div class="row">
                                          @if(Session::has('message'))
                                              <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
                                          @endif
                                          <form class="form-horizontal mt-4" method="post" action="{{isset($family) && ($family->pk_customer_family)?'/accountadmin/customers/family/update':'/accountadmin/customers/family/store'}}">
                                              @csrf
                                              <div class="row">
                                                  <div class="col-md-12">
                                                      <div class="form-group">
                                                          <label for="title">Name</label>
                                                          <input type="text" name="name"
                                                                 class="form-control @error('name') is-invalid @enderror"
                                                                 value="{{isset($family) && ($family->customer_family)?$family->customer_family:old('name')}}">
                                                          @error('name')
                                                          <span class="invalid-feedback" role="alert">
                                                                  <strong>{{ $message }}</strong>
                                                              </span>
                                                          @enderror
                                                      </div>
                                                  </div>
                                              </div>
                                              <div class="row">
                                                  <div class="col-md-6">
                                                      <div class="form-group">
                                                          <label for="title">RelationShip</label>
                                                          <select class="form-control @error('relationship') is-invalid @enderror" name="relationship">
                                                            @foreach($relationships as $relationship)
                                                            <option value="{{$relationship->pk_customer_family_relation}}" {{isset($family) && ($family->pk_customer_family_relation == $relationship->pk_customer_family_relation )?'selected':''}}>{{$relationship->customer_family_relation}}</option>
                                                            @endforeach
                                                          </select>
                                                          @error('relationship')
                                                          <span class="invalid-feedback" role="alert">
                                                                  <strong>{{ $message }}</strong>
                                                              </span>
                                                          @enderror
                                                      </div>
                                                  </div>

                                                  <div class="col-md-6">
                                                      <div class="form-group">
                                                          <label for="code">Day</label>
                                                          <select class="form-control @error('day') is-invalid @enderror" name="day">
                                                            @foreach($days as $day)
                                                            <option value="{{$day->pk_important_day}}" {{isset($family) && ($family->pk_important_day == $day->pk_important_day)?'selected':''}}>{{$day->important_day}}</option>
                                                            @endforeach
                                                          </select>
                                                          @error('day')
                                                          <span class="invalid-feedback" role="alert">
                                                                  <strong>{{ $message }}</strong>
                                                              </span>
                                                          @enderror
                                                      </div>
                                                  </div>

                                              </div>

                                              <div class="row">
                                                  <div class="col-md-4">
                                                      <div class="form-group">
                                                          <label for="start_at">Phone</label>
                                                          <input type="text" name="phone"
                                                                 class="form-control @error('phone') is-invalid @enderror"
                                                                 value="{{isset($family) && ($family->phone)?$family->phone:old('phone')}}">
                                                          @error('phone')
                                                          <span class="invalid-feedback" role="alert">
                                                                  <strong>{{ $message }}</strong>
                                                              </span>
                                                          @enderror
                                                      </div>
                                                  </div>

                                                  <div class="col-md-4">
                                                      <div class="form-group">
                                                          <label for="expire_at">Email</label>
                                                          <input type="text" name="email"
                                                                 class="form-control @error('email') is-invalid @enderror"
                                                                 value="{{isset($family) && ($family->email)?$family->email:old('email')}}">
                                                          @error('email')
                                                          <span class="invalid-feedback" role="alert">
                                                                  <strong>{{ $message }}</strong>
                                                              </span>
                                                          @enderror
                                                      </div>
                                                  </div>

                                                  <div class="col-md-4">
                                                      <div class="form-group">
                                                          <label for="expire_at">Date</label>
                                                          <input type="text" name="date"
                                                                 class="form-control @error('date') is-invalid @enderror" placeholder="MM/DD"
                                                                 value="{{isset($family) && ($family->date)?$family->date:old('date')}}">
                                                          @error('date')
                                                          <span class="invalid-feedback" role="alert">
                                                                  <strong>{{ $message }}</strong>
                                                              </span>
                                                          @enderror
                                                      </div>
                                                  </div>

                                              </div>
                                              <input type="hidden" name="pk_customers"
                                                     value="{{ isset($pk_customers ) && ($pk_customers->pk_customers)? $pk_customers->pk_customers : $family->pk_customers }}">
                                                     <input type="hidden" name="pk_customer_family"
                                                            value="{{isset($family) && ($family->pk_customer_family)?$family->pk_customer_family:''}}">
                                              <button class="btn btn-primary" type="submit">{{isset($family) && ($family->pk_customer_family)?'Update':'Submit'}}</button>
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
