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

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
@endsection

@section('content')

    <section>
        <div class="row match-height">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <x-form id="add-slider" method="POST" class="" :route="route('admin.labours.store')">
                    @csrf
                    <x-card>
                        <x-divider text="Basic Details" />
                        <div class="row">
                            <div class="col-lg-4  col-md-6">
                                <x-input name="name" label="Full Name" />
                            </div>
                            <div class="col-lg-4  col-md-6">
                                <x-input name="email" />
                            </div>
                            <div class="col-lg-4  col-md-6">
                                <x-input name="phone" type="number" />
                            </div>
                            <div class="col-lg-4  col-md-6">
                                <x-input-file name="profile_pic" />
                                <!--<x-input name="lat_long" class="lat_long"  />-->
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <label for="">Select State</label>
                                <select class="select2  form-control state-select " name="state">
                                    <option value="" selected disabled>Select State</option>
                                    @foreach ($states as $state)
                                        <option class="option-state-selected" value="{{ $state->id }}">
                                            {{ $state->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <label for="">Select State</label>
                                <select class="select2  form-control city-select" name="city">
                                    <option value="" selected disabled>Select City</option>

                                </select>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <label for="">Select Gender</label>
                                <select class="select2  form-control " name="gender">
                                    <option value="" selected disabled>Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>

                            <!--<div class="col-lg-4  col-md-6">-->
                            <!--    <x-input name="rate_per_day" type="number" />-->
                            <!--</div>-->

                            <div class="col-lg-12 col-md-12">
                                <x-input name="address" type="textarea" />
                            </div>


                        </div>
                        <x-divider text="Co-ordinate" />
                        <div id="map"></div>-
                        <x-divider text="Work Details" />
                        <div id="output"></div>
                        <div class="row">
                            <div class="col-lg-4 col-md-6">
                                <x-input type="time" class="start_time" name="start_time" />
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <x-input type="time" class="end_time" name="end_time" />
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <label for="">Labour Category</label>
                                <select class="select2 " name="category[]" multiple>
                                    @foreach ($category_data as $data)
                                        <option value="{{ $data->id }}">{{ $data->title }}</option>
                                    @endforeach
                                </select>
                            </div>



                            <!--<div class="col-lg-12 col-md-6">-->
                            <!--    <x-image-uploader name="labour_images" id="labour_images" />-->
                            <!--</div>-->

                            <!--<div class="col-lg-4 col-md-6">-->
                            <!--    <label for="">Preferred Shifts</label>-->
                            <!--    <select class="select2  form-control" name="shifts">-->
                            <!--        <option value="" disabled selected>Select Shift</option>-->
                            <!--        <option value="morning">Morning</option>-->
                            <!--        <option value="afternoon">Afternoon</option>-->
                            <!--        <option value="evening ">Evening</option>-->
                            <!--        <option value="night">Night</option>-->
                            <!--    </select>-->
                            <!--</div>-->



                        </div>
                        <x-divider text="KYC Details" />
                        <div class="row">
                            <div class="col-lg-4 col-md-6">
                                <x-input name="aadhaar_number" type="number" />
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <x-input-file name="aadhaar_card_front" />
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <x-input-file name="aadhaar_card_back" />
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <x-input name="pan_number" />
                            </div>
                        </div>
                        <x-divider text="Bank Details" />
                        <div class="row">
                            <div class="col-lg-4 col-md-6">
                                <x-input name="bank_name" />
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <x-input name="IFSC_code" />
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <x-input name="bank_address" />
                            </div>
                        </div>

                    </x-card>
                </x-form>
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
            center: { lat: 19.0760, lng: 72.8777 }, // Default center position
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
                center: { lat: lat, lng: lng },
                radius: radius,
                map: map,
                fillColor: '#FF0000',
                fillOpacity: 0.35,
                strokeColor: '#FF0000',
                strokeOpacity: 0.8,
                strokeWeight: 2
            });

            // Output the center and radius
            // entering latitude
            document.getElementById("latitude").innerHTML = lat
            document.getElementById("longitude").value = lng
            document.getElementById("radius").value = radius

           
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

    <script>
        $(document).ready(function() {
            $(".state-select").on("change", function() {
                let state_id = $(".state-select").val()

                $.ajax({
                    type: "GET",
                    data: {
                        state_id: state_id
                    },
                    url: '{{ route('admin.labours.city') }}',
                    success: function(response) {

                        $(".city-select").empty()

                        $(".city-select").append(
                            `<option value="" selected disabled>Select City</option>`)

                        response.forEach((data) => {
                            $(".city-select").append(`
            <option value="${data.id}">${data.name}</option>
        `);
                        });
                    }
                })
            })
        })
    </script>

    <script>
        $(document).ready(function() {
            $(".start_time").on("change", function() {
                let start_time = $(this).val();

            });


            $(".end_time").on("change", function() {
                let end_time = $(this).val();
            });







        })
    </script>

    <script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>

    <script src="https://unpkg.com/jquery-filepond/filepond.jquery.js"></script>

    <script>
        // var map = L.map('map').setView([19.0760, 72.8777], 13);
        // L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        //     maxZoom: 19,
        //     attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        // }).addTo(map);

        // var currentMarker = null;

        // function onMapClick(e) {
        //     if (currentMarker !== null) {
        //         map.removeLayer(currentMarker);
        //     }

        //     currentMarker = L.marker(e.latlng).addTo(map);
        //     alert("You clicked the map at Latitude: " + e.latlng.lat + ", Longitude: " + e.latlng.lng);

        //     var lat_long = document.querySelector(".lat_long")
        //     lat_long.value = e.latlng.lat + "," + e.latlng.lng

        //     currentMarker.bindPopup("You clicked the map at " + e.latlng.toString()).openPopup();
        // }

        // map.on('click', onMapClick);
    </script>
@endsection
