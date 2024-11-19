@extends('layouts/contentLayoutMaster')

@section('title', 'Labour')
@section('page-style')
    <style>
        [x-cloak] {
            display: none !important;
        }

        .dropdown-menu {
            transform: scale(1) !important;
        }
    </style>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection

@section('content')


    <section>
        <div class="row match-height">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <x-card>
                    {{-- <form action="{{ route('admin.users.labour-export') }}" method="GET"> --}}
                    <div class="d-flex justify-content-end align-items-center mb-1">
                        <input type="hidden" name="type" value="{{ $type }}" />
                        <div class="col-md-3">
                            <div class="form-group  text-left">
                                <label for="month">Date </label>
                                <input value="" type="text" class="form-control flatpickr flatpickr-input"
                                    required="" name="month" id="month" placeholder=" Enter Date"
                                    readonly="readonly">
                                <div class="invalid-tooltip">Please provide a valid Month</div>


                            </div>



                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary" onclick="exportData()">Export</button>
                        </div>
                    </div>
                    {{-- </form> --}}
                    <livewire:labour-table type="{{ $type }}" />
                </x-card>
            </div>
        </div>
    </section>


    <x-side-modal title="Add Labour" id="add-blade-modal">
        <x-form id="add-slider" method="POST" class="" :route="route('admin.labours.store')">
            <div class="col-md-12 col-12 ">
                <x-input name="name" />
                <x-input name="phone" />
                <x-input name="pan" />
                <x-input name="bank_name" />
                <x-input name="add" />
                <x-input name="ifsc_code" />
                <x-input name="rate" />
                <x-input-file name="profile_pic" />
                <x-input-file name="addhar_card_front" />
                <x-input-file name="addhar_card_back" />
            </div>
        </x-form>
    </x-side-modal>
    <x-side-modal title="Update Slider" id="edit-slider-modal">
        <x-form id="edit-Slider" method="POST" class="" :route="route('admin.sliders.update')">
            <div class="col-md-12 col-12 ">
                <x-input name="title" />
                <x-input-file name="image" />
                <x-input name="id" type="hidden" />
            </div>

        </x-form>
    </x-side-modal>
    <x-modal title="Export Password" footer="false" id="export-password">
        <x-slot name="body">
            <x-form successCallback="test" id="add-password" method="POST" class="" :route="route('admin.users.verifyPassword')">
                <x-input name="password"></x-input>
            </x-form>
        </x-slot>
    </x-modal>
@endsection
@section('page-script')
    <script>
        function exportData() {
            $('#export-password').modal('show');
        }
        $(document).ready(function() {
            $(document).on('click', '[data-show]', function() {
                const modal = $(this).data('show');
                // $(`#${modal}`).modal('show');
                window.location.href = "{{ route('admin.labours.add') }}"
            });
        });


        // $(document).on('click', function(){
        //     $('.drop-menuToggle').removeClass('active');
        // })

        function setValue(data, modal) {
            // $(`${modal} #id`).val(data.id);
            // $(`${modal} #title`).val(data.title);
            // $(`${modal} #image`).val(data.image);

            $(modal).modal('show');
        }

        $(".flatpickr").flatpickr({
            mode: "range"
        });


        function test(response) {


            if (response.success === true) {

                var month = $('#month').val();
                // alert("hi");


                window.location.href = "{{ url('admin/users/labour-export?month=') }}" + month

            }

        }
    </script>
@endsection
