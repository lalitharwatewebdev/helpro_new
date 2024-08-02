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
                <form action="{{ route('admin.labours.store') }}" method="POST">
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

                            <div class="col-lg-4  col-md-6">
                                <x-input name="rate_per_day" type="number" />
                            </div>


                        </div>
                        <x-divider text="KYC Details" />
                        <div class="row">
                            <div class="col-lg-4 col-md-6">
                                <x-input name="aadhaar_number" type="number" />
                            </div>
                            {{-- <div class="col-lg-4 col-md-6">
                                <x-input-file name="aadhaar_card_front" />
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <x-input-file name="aadhaar_card_back" />
                            </div> --}}
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
                            <div class="col-12 d-flex justify-content-center">


                                <button class="btn btn-primary">Submit</button>

                            </div>
                        </div>

                    </x-card>
                </form>
            </div>
        </div>
    </section>

@endsection
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
            console.log(data);
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

                        $(".city-select").append(`<option value="" selected disabled>Select City</option>`)

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
@endsection
