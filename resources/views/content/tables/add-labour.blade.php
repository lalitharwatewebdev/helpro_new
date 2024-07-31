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
                <form action="{{route("admin.labours.store")}}" method="POST" >
                    @csrf
                <x-card>
                    <x-divider text="Basic Details"/>
                    <div class="row">
                        <div class="col-lg-4  col-md-6">
                            <x-input name="full_name"/>
                        </div>
                        <div class="col-lg-4  col-md-6">
                            <x-input name="email"/>
                        </div>
                        <div class="col-lg-4  col-md-6">
                            <x-input name="phone" type="number"/>
                        </div>
                        <div class="col-lg-4  col-md-6">
                            <x-input-file name="profile_image" type="number"/>
                        </div>
                    </div>
                    <x-divider text="KYC Details" />
                    <div class="row">
                        <div class="col-lg-4 col-md-6">
                            <x-input name="aadhaar_number" type="number"/>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <x-input-file name="aadhaar_card_front"/>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <x-input-file name="aadhaar_card_back"/>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <x-input-file name="pan_number"/>
                        </div>
                    </div>
                    <x-divider text="Bank Details"/>
                    <div class="row">
                        <div class="col-lg-4 col-md-6">
                            <x-input name="bank_name"/>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <x-input name="IFSC_code"/>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <x-input name="bank_address"/>
                        </div>
                        <div class="d-flex justify-content-center">
                        <button class="btn btn-primary d-inline-block">Submit</button>
                    </div>
                    </div>
                    <x-divider text="Order List/Booking"/>
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
@endsection
