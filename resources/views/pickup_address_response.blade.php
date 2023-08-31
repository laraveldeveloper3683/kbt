@foreach($stores->cursor() as $store)
    <div class="col-md-12 mb-3 store1" id="pickupStore-{{ $store->pk_locations }}">
        <div class="row">
            <div class="col-md-12">
                <h6>{{ $store->location_name }}</h6>
            </div>
            @php
                $distance = number_format($store->calculated_distance, 1) . ' mi';
            @endphp
            <div class="col-md-12">
                <p>{{ $distance }}</p>
            </div>
            <div class="col-md-12">
                <p>
                    {{ $store->address . ' ,' . $store->address_1 . ' ,' . $store->city . ' ,' . $store->zip . ' ,' .
                    $store->state_name . ' ,' . $store->country_name }}
                </p>
            </div>
        </div>
        <div class="selectTimeItem">
            <div class="row">
                <div class="col-md-10">
                    @if($store->locationTime->count())
                        @foreach($store->locationTime as $locationTime)
                            {{ 'Day - ' . @$locationTime->day . ' , ' . @date('h:i A', @strtotime(@$locationTime->open_time)) . ' -
                        ' . @date('h:i A', strtotime($locationTime->close_time)) }}
                            @if(!$loop->last)
                                <br>
                            @endif
                        @endforeach
                    @endif
                </div>
                @php
                    $currentLocationTime = $store->locationTime->where('day', date('l'))->first();
                @endphp
                <div class="col-md-2">
                    <input type="radio" required name="store_id"
                           value="{{ @$currentLocationTime->pk_location_times . '/' . $store->pk_locations }}"
                           data-taxRate="{{ $store->tax_rate }}" data-storeId="{{ $store->pk_locations }}"
                           data-locationTime="{{ @$locationTime->pk_location_times }}" class="pickup-store-checkbox"
                           id="pickup-store-checkbox-{{ $store->pk_locations }}"
                           data-distance="{{ $distance }}"
                           data-calcDistance="{{ number_format($store->calculated_distance, 1) }}"> Select
                </div>
            </div>
        </div>
    </div>
@endforeach
