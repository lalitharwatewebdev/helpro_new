@extends('layouts/contentLayoutMaster')

@section('title', 'Promo Code')
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
                    <livewire:promo-code-table />
                </x-card>
            </div>
        </div>
    </section>


    <x-side-modal title="Add Promo Code" id="add-blade-modal">
        <x-form id="add-slider" method="POST" class="" :route="route('admin.promo-code.store')">
            <div class="col-md-12 col-12 ">
                <label for="">Promo Code Type</label>
                <select name="type" id="" class="form-control select2">
                    <option value="">   </option>
                    <option value="percentage">Percentage(%)</option>
                    <option value="number">Number</option>
                </select>
                <x-input name="amount" />
                <x-input name="promo_code_title" />
               
            </div>
        </x-form>
    </x-side-modal>
    <x-side-modal title="Update Promo Code" id="edit-promo-code-modal">
        <x-form id="edit-slider-modal" method="POST" class="" :route="route('admin.promo-code.update')">
            <div class="col-md-12 col-12 ">
                <label for="">Promo Code Type</label>
                <select name="type" id="" class="form-control select2">
                    <option value="">   </option>
                    <option value="percentage">Percentage(%)</option>
                    <option value="number">Number</option>
                </select>
                <x-input name="amount" />
                <x-input name="id" type="hidden" />
                <x-input name="promo_code_title" />
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
            $(`${modal} #promo_code_title`).val(data.title);
            $(`${modal} #amount`).val(data.number);

            $(modal).modal('show');
        }
    </script>


    <script>
        $(".select2").select2()
    </script>
@endsection
