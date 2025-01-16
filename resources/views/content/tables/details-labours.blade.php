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
                <div id="accordion">
                    <div class="card">
                        <div class="card-header d-flex justify-content-center" id="headingOne">
                            <h5 class="mb-0 text-center display-4">
                                <button class="btn btn-link fw-bold " data-toggle="collapse" data-target="#collapseOne"
                                    aria-expanded="true" aria-controls="collapseOne">
                                    <span>Labour Details</span>
                                </button>
                            </h5>
                        </div>

                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                            <div class="card-body">

                                <x-card>
                                    <div class="d-flex justify-content-center">
                                        <div class="col-md-6">
                                            <x-form x-form id="add-slider" method="POST" class="" :route="route('admin.labours.update')">
                                                <x-input type="hidden" name="id" value="{{ $data->id }}" />
                                                <label for="">Labour Type</label>
                                                <select name="type" class="form-control select2" id="">
                                                    <option value="pending"
                                                        {{ $data->labour_status == 'pending' ? 'selected' : '' }}>Pending
                                                    </option>
                                                    <option value="accepted"
                                                        {{ $data->labour_status == 'accepted' ? 'selected' : '' }}>Approved
                                                    </option>
                                                    <option value="rejected"
                                                        {{ $data->labour_status == 'rejected' ? 'selected' : '' }}>Rejected
                                                    </option>
                                                </select>
                                            </x-form>
                                        </div>
                                    </div>
                                    <x-divider text="Labour Basic Details" />
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
                                            <span> <b>State:</b> {{ $data->states->name ?? '' }} </span>
                                        </div>

                                        <div class="col-lg-3 col-md-6">
                                            <span> <b>City:</b> {{ $data->cities->name ?? '' }} </span>
                                        </div>

                                        <div class="col-lg-3 col-md-6">
                                            <span> <b>Address:</b> {{ $data->address }} </span>
                                        </div>

                                        <div class="col-lg-3 col-md-6">
                                            <span> <b>Pincode:</b> {{ $data->pin_code ?? '' }} </span>
                                        </div>
                                        <div class="col-lg-3 col-md-6">
                                            <span> <b>Category:</b> {{ $data->category[0]->title ?? '' }} </span>
                                        </div>

                                        <div class="col-lg-3 col-md-6">
                                            <span> <b>Profile Image:</b>
                                                @if (!is_null($data->profile_pic))
                                                    <img src="{{ asset("$data->profile_pic") }}"
                                                        class="view-on-click  rounded-circle" style="width:40px">
                                                @else
                                                    <img src="{{ asset('images/placeholder.jpg') }}"
                                                        class="view-on-click   rounded-circle" style="width:40px">
                                                @endif
                                            </span>
                                        </div>

                                        <x-divider text="Labour KYC Details" />

                                        <div class="col-lg-4 col-md-6 mb-2">
                                            <span> <b>Aadhaar Number:</b> {{ $data->aadhaar_number }} </span>
                                        </div>

                                        <div class="col-lg-4 col-md-6 mb-2">
                                            <span> <b>PAN Number:</b> {{ $data->pan_card_number }} </span>
                                        </div>
                                        <div class="col-lg-4 col-md-6 mb-2">
                                            <span> <b>Pan Card Image:</b>
                                                @if (!is_null($data->pan_front))
                                                    <img src="{{ asset("$data->pan_front") }}"
                                                        class="view-on-click  rounded-circle" style="width:40px">
                                                @else
                                                    <img src="{{ asset('images/placeholder.jpg') }}"
                                                        class="view-on-click   rounded-circle" style="width:40px">
                                                @endif
                                            </span>
                                        </div>


                                        <div class="col-lg-4 col-md-6 mb-2">
                                            <span> <b>Aadhaar Card Front:</b>
                                                @if (!is_null($data->aadhaar_card_front))
                                                    <img src="{{ asset("$data->aadhaar_card_front") }}"
                                                        class="view-on-click  rounded-circle" style="width:40px">
                                                @else
                                                    <img src="{{ asset('images/placeholder.jpg') }}"
                                                        class="view-on-click   rounded-circle" style="width:40px">
                                                @endif
                                            </span>
                                        </div>

                                        <div class="col-lg-4 col-md-6 mb-2">
                                            <span> <b>Aadhaar Card Back:</b>
                                                @if (!is_null($data->aadhaar_card_back))
                                                    <img src="{{ asset("$data->aadhaar_card_back") }}"
                                                        class="view-on-click  rounded-circle" style="width:40px">
                                                @else
                                                    <img src="{{ asset('images/placeholder.jpg') }}"
                                                        class="view-on-click   rounded-circle" style="width:40px">
                                                @endif
                                            </span>
                                        </div>

                                        <x-divider text="Labour Bank Details" />
                                        <div class="col-lg-4 col-md-6 mb-2">
                                            <span> <b>Bank Name:</b> {{ $data->bank_name }} </span>
                                        </div>

                                        <div class="col-lg-4 col-md-6 mb-2">
                                            <span> <b>IFSC Code:</b> {{ $data->IFSC_code }} </span>
                                        </div>
                                        {{-- 
                                        <div class="col-lg-4 col-md-6 mb-2">
                                            <span> <b>Bank Address:</b> {{ $data->branch_address }} </span>
                                        </div> --}}

                                        <div class="col-lg-4 col-md-6 mb-2">
                                            <span> <b>Account Number:</b> {{ $data->account_number }} </span>
                                        </div>

                                        <x-divider text="Work Details" />
                                        <div class="col-lg-4 col-md-6 mb-2">
                                            <span> <b>Rate per Day:</b> {{ $data->rate_per_day }} </span>
                                        </div>
                                        <div class="col-lg-4 col-md-6 mb-2">
                                            <span> <b>Start Time:</b> {{ $data->start_time }} </span>
                                        </div>
                                        <div class="col-lg-4 col-md-6 mb-2">
                                            <span> <b>End Time:</b> {{ $data->end_time }} </span>
                                        </div>

                                        <div class="col-lg-4 col-md-6 mb-2">
                                            <span> <b>Qualification: </b> {{ $data->qualification }} </span>
                                        </div>

                                        <div class="col-lg-4 col-md-6 mb-2">
                                            <span> <b>Preferred Shift: </b> {{ $data->preferred_shift }} </span>
                                        </div>

                                        <div class="col-lg-4 col-md-6 mb-2">
                                            <span> <b>Labour Status: </b> {{ $data->labour_status }} </span>
                                        </div>

                                    </div>
                                </x-card>

                            </div>
                        </div>
                    </div>
                </div>

                <div id="accordion">
                    <div class="card">
                        <div class="card-header d-flex justify-content-center" id="headingOne">
                            <h5 class="mb-0 text-center display-4">
                                <button class="btn btn-link fw-bold " data-toggle="collapse" data-target="#collapseTwo"
                                    aria-expanded="true" aria-controls="collapseOne">
                                    <span>Labour Booked Details</span>
                                </button>
                            </h5>
                        </div>

                        <div id="collapseTwo" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                            <div class="card-body">
                                <x-card>
                                    <x-divider text="User Booking Details" />
                                    @php
                                        $user_id = request()->query('id');
                                    @endphp

                                    <livewire:labour-booking-table user_id="{{ $user_id }}" type="labour" />
                                </x-card>
                            </div>
                        </div>
                    </div>
                </div>
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
