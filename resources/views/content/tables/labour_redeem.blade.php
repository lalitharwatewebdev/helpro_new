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
                            <input type="hidden" id="type" name="type"
                                value="{{ Request()->payment_status ?? '' }}">
                    </div>
                    <livewire:labour-redeem-table />
                </x-card>
            </div>
        </div>
    </section>
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

        function test(response) {
            var type = $('#type').val();

            if (response.success === true) {
                window.location.href = "{{ url('admin/redeem/export?type=') }}" + type;

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

        function changeStatus(mod) {

            var status = $(mod).val();
            var id = $(mod).data('id');

            console.log(status);
            console.log(id);


            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                }
            });
            $.ajax({
                type: "POST",
                url: "{{ route('admin.redeem.accept-redeem') }}",
                data: {
                    status: status,
                    id: id,

                },
                success: function(response) {
                    console.log(response);

                }
            });
        }
    </script>
@endsection
