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
                    <livewire:subscription-table />
                </x-card>
            </div>
        </div>
    </section>


    <x-side-modal title="Add Subscription" id="add-blade-modal">
        <x-form id="add-category" method="POST" class="" :route="route('admin.subscriptions.store')">
            <div class="col-md-12 col-12 ">
                <x-input name="title" />
                <x-input name="amount" type="number" />
                <x-input name="days" type="number" />
            </div>
        </x-form>
    </x-side-modal>
    <x-side-modal title="Update Subscription" id="edit-subscription-modal">
        <x-form id="edit-subscription-modal" method="POST" class="" :route="route('admin.subscriptions.update')">
            <div class="col-md-12 col-12 ">
                <x-input name="title" />
                <x-input name="amount" type="number" />
                <x-input name="days" type="number" />
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
            $(`${modal} #title`).val(data.title);
            $(`${modal} #amount`).val(data.amount);
            $(`${modal} #days`).val(data.days);
            $(modal).modal('show');
        }
    </script>
@endsection
