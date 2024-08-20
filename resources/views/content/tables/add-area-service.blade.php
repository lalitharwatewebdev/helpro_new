@extends('layouts/contentLayoutMaster')

@section('title', 'Users')
@section('page-style')
    <style>
        [x-cloak] {
            display: none !important;
        }

        .dropdown-menu {
            transform: scale(1) !important;
        }

        #map {
            height: 380px;
        }
    </style>


@endsection

@section('content')

    <section>
        <div class="row match-height">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <x-card>
                    <x-form id="add-slider" method="POST" class="" :route="route('admin.areas.store')">
                        @csrf

                        <div class="col-lg-4 col-md-6">
                            <x-input name="latitude" />
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <x-input name="longitude" />
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <x-input name="radius" />
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <x-input name="price" type="number" />
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <x-input name="area_name" />
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <label for="">Category</label>
                            <select name="category" id="" class="form-control select2">
                                <option value="" selected>Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 mt-4">
                            <div id="map"></div>
                        </div>

                    </x-form>
                </x-card>
            </div>
        </div>
    </section>

@endsection
@pushonce('component-script')
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
@endpushonce
@section('page-script')
    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap">
    </script>

    <script>
        var map;
        var circle;

        // Initialize the map
        function initMap() {
            var mapOptions = {
                center: {
                    lat: 19.0760,
                    lng: 72.8777
                }, // Default center position
                zoom: 8 // Default zoom level
            };

            map = new google.maps.Map(document.getElementById('map'), mapOptions);

            // Add a click event listener to the map
            map.addListener('click', function(event) {
                var lat = event.latLng.lat();
                var lng = event.latLng.lng();

                // Define the radius (in meters)
                var radius = 5000; // Example: 1000 meters

                // Remove the previous circle if it exists
                if (circle) {
                    circle.setMap(null);
                }

                // Create a new circle
                circle = new google.maps.Circle({
                    center: {
                        lat: lat,
                        lng: lng
                    },
                    radius: radius,
                    map: map,
                    fillColor: '#FF0000',
                    fillOpacity: 0.35,
                    strokeColor: '#FF0000',
                    strokeOpacity: 0.8,
                    strokeWeight: 2
                });

                $("#latitude").val(lat)
                $("#longitude").val(lng)
                $("#radius").val(radius)

                // Output the center and radius
                // document.getElementById('output').innerHTML =
                //     'Center: Latitude ' + lat + ', Longitude ' + lng + '<br>' +
                //     'Radius: ' + radius + ' meters';
            });
        }
    </script>
    <script>
        $(document).ready(function() {
            // $('#users-table_wrapper .dt-buttons').append(
            //     `<button type="button" data-show="add-users-modal" class="btn btn-flat-success border border-success waves-effect float-md-right">Add</button>`
            // );
            // $(document).on('click', '[data-show]', function() {
            //     const modal = $(this).data('show');
            //     $(`#${modal}`).modal('show');
            // });
        });

        function setValue(data, modal) {
            // console.log(data);
            // $(modal + ' #id').val(data.id);
            // $(modal + ' #name').val(data.name);
            // $(modal + ' #phone').val(data.phone);
            // $(modal + ' #email').val(data.email);
            // $(modal + ' #phone').val(data.phone);
            // $(modal + ' #address').val(data.address);
            // $(modal + ' [name=gender][value=' + data.gender + ']').prop('checked', true).trigger('change');
            // $(modal).modal('show');
        }
    </script>


@endsection
