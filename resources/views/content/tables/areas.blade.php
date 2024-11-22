@extends('layouts/contentLayoutMaster')

@section('title', 'Areas')
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
                    <div class="col-md-3 mb-2">
                        <!--<button class="btn mb-1 btn-primary text-end" href={{ route('admin.users.export') }}>Export</a>-->
                        <button class="btn  btn-primary text-end" onclick="exportData()">Export</a>
                    </div>
                    <livewire:areas-table />
                </x-card>
            </div>
        </div>
    </section>


    <x-side-modal title="Add Cart" id="add-blade-modal">
        <x-form id="add-category" method="POST" class="" :route="route('admin.carts.store')">
            <div class="col-md-12 col-12 ">
                <x-input name="title" />
                <x-input-file name="image" />
            </div>
        </x-form>
    </x-side-modal>
    <x-side-modal title="Update Cart" id="edit-cart-modal">
        <x-form id="edit-category-modal" method="POST" class="" :route="route('admin.carts.update')">
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
        $(document).ready(function() {
            $(document).on('click', '[data-show]', function() {
                const modal = $(this).data('show');
                window.location.href = "{{ route('admin.areas.add-areas') }}"
            });
        });


        // $(document).on('click', function(){
        //     $('.drop-menuToggle').removeClass('active');
        // })

        function exportData() {
            $('#export-password').modal('show');
        }

        function test(response) {
            if (response.success === true) {
                window.location.href = "{{ url('admin/areas/export') }}";
            }

        }

        function setValue(data, modal) {
            // $(`${modal} #id`).val(data.id);
            // $(`${modal} #title`).val(data.title);
            // // $(`${modal} #image`).val(data.image);

            // $(modal).modal('show');

        }
    </script>
@endsection
