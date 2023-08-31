<div class="col-md-12">
    <h4 class="font-weight-bolder">
        <i class="fa fa-map-marker"></i> Pickup Address
    </h4>
    <hr>
</div>
@foreach($stores->cursor() as $store)
    @if($store->locationTime->count())
        <div class="col-md-12 mb-3 store1" id="pickupStore-{{ $store->pk_locations }}">
            @php
                $distance = number_format($store->calculated_distance, 1) . ' mi';
            @endphp
            <div class="row">
                <div class="col-md-12">
                    <h5>
                        {{ $store->location_name }} -
                        <small class="d-inline small">({{ $distance }})</small>

                        <div class="form-group form-inline d-inline small float-right mt-n2 mr-2">
                            <label for="pickup-store-checkbox-{{ $store->pk_locations }}">
                                <input type="radio" required name="store_id"
                                       value="{{ $store->pk_locations }}"
                                       data-taxRate="{{ $store->tax_rate }}"
                                       data-storeId="{{ $store->pk_locations }}"
                                       class="form-control pickup-store-checkbox"
                                       id="pickup-store-checkbox-{{ $store->pk_locations }}">
                                Select
                            </label>
                        </div>
                    </h5>
                    <p>
                        <strong>Address:</strong> {{ $store->address . ' ,' . $store->address_1 . ' ,' . $store->city . ' ,' . $store->zip . ' ,' .
                    $store->state_name . ' ,' . $store->country_name }}
                    </p>
                </div>
            </div>

            @if($store->locationTime->count())
                <div class="selectTimeItem" id="selectTimeItem{{ $store->pk_locations }}" style="display: none;">
                    <div class="row">
                        @foreach($store->locationTime as $locationTime)
                            <div class="col-md-12">
                                {{ 'Day - ' . @$locationTime->day . ' , ' . @date('h:i A', @strtotime(@$locationTime->open_time)) . ' -
                                ' . @date('h:i A', strtotime($locationTime->close_time)) }}

                                <div class="form-group form-inline d-inline small float-right mt-n3 mr-3">
                                    <label for="pickup-store-time-checkbox-{{ $locationTime->pk_location_times }}">
                                        <input type="radio" required name="store_time_id"
                                               value="{{ $locationTime->pk_location_times }}"
                                               data-storeId="{{ $store->pk_locations }}"
                                               data-storeTimeId="{{ $locationTime->pk_location_times }}"
                                               class="pickup-store-time-checkbox form-control"
                                               id="pickup-store-time-checkbox-{{ $locationTime->pk_location_times }}">
                                        Select
                                    </label>
                                </div>
                                <hr>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif
@endforeach
