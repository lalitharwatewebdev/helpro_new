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
    </style>

@endsection

@section('content')

    <section>
        <div class="row match-height">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <x-card>


                    <x-divider text="User Details" />
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-2">
                            <span> <b>Name:</b> {{ $data->name }} </span>
                        </div>

                        <div class="col-lg-3 col-md-6 mb-2">
                            <span> <b>Email:</b> {{ $data->email }} </span>
                        </div>

                        <div class="col-lg-3 col-md-6 mb-2">
                            <span> <b>Phone:</b> {{ $data->phone }} </span>
                        </div>

                        <div class="col-lg-3 col-md-6 mb-2">
                            <span> <b>Gender:</b> {{ ucFirst($data->gender) }} </span>
                        </div>

                        <div class="col-lg-3 col-md-6 mb-2">
                            <span> <b>Profile Image:</b>
                                @if (!is_null($data->profile_pic))
                                    <img src="{{ asset("$data->profile_pic") }}" class="view-on-click  rounded-circle"
                                        style="width:40px">
                                @else
                                    <img src="{{ asset('images/placeholder.jpg') }}" class="view-on-click   rounded-circle"
                                        style="width:40px">
                                @endif
                            </span>
                        </div>

                        {{-- <div class="col-lg-3 col-md-6 mb-2">
                            <span> <b>State:</b> {{ $data->states->name }} </span>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <span> <b>City:</b> {{ $data->cities->name }} </span>
                        </div> --}}

                        {{-- <div class="col-lg-3 col-md-6">
                            <span> <b>Address:</b> {{ $data->address }} </span>
                        </div> --}}

                        <x-divider text="User Address" />
                        @foreach ($data->addresses as $address)
                            <div class="col-lg-12 col-md-6">
                                <span> <b>Address {{ $loop->iteration }} </b>
                                    @if ($address->is_primary == 'yes')
                                        <span class="bg-primary rounded-pill text-white"
                                            style="padding:2px 4px;font-size:10px">Primary</span>
                                        {{-- <h4>saf</h4> --}}
                                    @endif
                                    <br>
                                    <p>{{ $address->address }}
                                        <br>
                                        <span><b>State: </b>{{ $address->states->name }}</span>
                                        <br>
                                        <span><b>City: </b>{{ $address->cities->name }}</span>
                                        <br>
                                        <span><b>Pincode: </b>{{ $address->pincode }}</span>

                                    </p>

                                </span>
                                <hr>
                            </div>
                        @endforeach
                    </div>
                </x-card>

                <x-card>
                    <x-divider text="User Booking Details" />
                    @php
                        $user_id = request()->query('id');

                    @endphp

                    <livewire:user-booking-table user_id="{{ $user_id }}" type="user" />
                </x-card>
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
                        papi
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
        $('.my-pond').filepond();
    </script>
@endsection
