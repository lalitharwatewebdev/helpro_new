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
                    <livewire:category-table />
                </x-card>
            </div>
        </div>
    </section>


    <x-side-modal title="Add Category" id="add-blade-modal">
        <x-form id="add-category" method="POST" class="" :route="route('admin.category.store')">
            <div class="col-md-12 col-12 ">
                <x-input name="title" />
                <x-input name="price" />
                <x-input-file name="image" />
            </div>
        </x-form>
    </x-side-modal>
    <x-side-modal title="Update Category" id="edit-category-modal">
        <x-form id="edit-category-modal" method="POST" class="" :route="route('admin.category.update')">
            <div class="col-md-12 col-12 ">
                <x-input name="title" id="edit_title" />
                <x-input name="price" id="edit_price" />
                <x-input-file name="image" />
                <x-input name="id" type="hidden" />
            </div>

        </x-form>
    </x-side-modal>
@endsection
@section('page-script')
    <script>
        $(document).ready(function() {
            $(document).on('click', '[data-show]', function() {
                const modal = $(this).data('show');
                $(`#${modal}`).modal('show');
            });
        });


        // $(document).on('click', function(){
        //     $('.drop-menuToggle').removeClass('active');
        // })

        function setValue(data, modal) {
            $(`${modal} #id`).val(data.id);
            $(`${modal} #edit_title`).val(data.title);
            $(`${modal} #edit_price`).val(data.price);

            $(modal).modal('show');
        }
    </script>
@endsection
