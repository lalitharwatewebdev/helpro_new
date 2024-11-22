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
                    <div class="col-md-3 mb-2">
                        <!--<button class="btn mb-1 btn-primary text-end" href={{ route('admin.users.export') }}>Export</a>-->
                        <button class="btn  btn-primary text-end" onclick="exportData()">Export</a>
                    </div>
                    <livewire:user-booking-table type="{{ $type }}" />
                    <input type="hidden" name="type" id="type" value="{{ $type ?? '' }}">
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

            $(document).on('change', '#booking_status', function() {
                var status = $(this).val();
                var id = $(this).data('id');

                console.log("status");
                console.log(status);
                console.log(id);
                $.ajaxSetup({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                    }
                });


                $.ajax({
                    type: 'post',
                    url: "{{ route('admin.userbookings.changeStatus') }}",
                    data: {
                        status: status,
                        id: id,

                    },
                    success: function(response) {
                        window.location.href = "{{ route('admin.userbookings.pending') }}"
                    }
                });
            });
        });

        function test(response) {
            var type = $('#type').val();
            if (response.success === true) {
                window.location.href = "{{ url('admin/userbookings/export?type=') }}" + type;
            }
        }


        // $(document).on('click', function(){
        //     $('.drop-menuToggle').removeClass('active');
        // })

        function setValue(data, modal) {
            // $(`${modal} #id`).val(data.id);
            // $(`${modal} #title`).val(data.title);
            // $(`${modal} #image`).val(data.image);

            $(modal).modal('show');
        }
    </script>
@endsection
