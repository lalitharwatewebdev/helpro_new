@extends('layouts/contentLayoutMaster')

@section('title', 'Category')
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
                    <livewire:category-table />
                </x-card>
            </div>
        </div>
    </section>


    <x-side-modal title="Add Category" id="add-blade-modal">
        <x-form id="add-category" method="POST" class="" :route="route('admin.category.store')">
            <div class="col-md-12 col-12 ">
                <x-input name="title" />
                {{-- <x-input name="price" /> --}}
                <x-input name="percentage_for_less_than" type="number" label="Percentage for Less Than 12 Hours" />
                <x-input name="percentage_for_more_than" type="number" label="Percentage for More than 12 Hours" />
                <x-input-file name="image" />
                <x-input name="note" id="note" type="textarea" label="Note" />

            </div>
        </x-form>
    </x-side-modal>
    <x-side-modal title="Update Category" id="edit-category-modal">
        <x-form id="edit-category-modal" method="POST" class="" :route="route('admin.category.update')">
            <div class="col-md-12 col-12 ">
                <x-input name="title" id="edit_title" />
                <x-input name="percentage_for_less_than" id="edit_percentage_for_less_than" type="number"
                    label="Percentage for Less Than 12 Hours" />
                <x-input name="percentage_for_more_than" id="edit_percentage_for_more_than" type="number"
                    label="Percentage for More than 12 Hours" />
                <x-input-file name="image" />
                <x-input name="note" id="edit_note" type="textarea" label="Note" />

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
                $(`#${modal}`).modal('show');
            });
        });

        function test(response) {
            console.log(response.success);
            console.log(response.success === true);


            if (response.success === true) {
                console.log("inser");

                var month = $('#month').val();
                // alert("hi");


                window.location.href = "{{ url('admin/category/export') }}";

            }

        }

        // $(document).on('click', function(){
        //     $('.drop-menuToggle').removeClass('active');
        // })

        function setValue(data, modal) {
            $(`${modal} #id`).val(data.id);
            $(`${modal} #edit_title`).val(data.title);
            $(`${modal} #edit_percentage_for_less_than`).val(data.percentage_for_less_than);
            $(`${modal} #edit_percentage_for_more_than`).val(data.percentage_for_more_than);
            $(`${modal} #edit_note`).val(data.note);


            // $(`${modal} #edit_title`).val(data.title);

            // $(`${modal} #edit_price`).val(data.price);

            $(modal).modal('show');
        }
    </script>
@endsection
