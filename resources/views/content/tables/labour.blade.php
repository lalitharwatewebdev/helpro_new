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
@endsection

@section('content')


<section>
    <div class="row match-height">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <x-card>
                <livewire:labour-table />
            </x-card>
        </div>
    </div>
</section>


<x-side-modal title="Add Labour" id="add-blade-modal">
    <x-form id="add-slider" method="POST" class="" :route="route('admin.labours.store')">
        <div class="col-md-12 col-12 ">
            <x-input name="name" />
            <x-input name="phone" />
            <x-input name="pan"/>
            <x-input name="bank_name"/>
            <x-input name="add"/>
            <x-input name="ifsc_code"/>
            <x-input name="rate"/>
            <x-input-file name="profile_pic"/>
            <x-input-file name="addhar_card_front"/>
            <x-input-file name="addhar_card_back"/>
        </div>
    </x-form>
</x-side-modal>
<x-side-modal title="Update Slider" id="edit-slider-modal">
    <x-form id="edit-Slider" method="POST" class="" :route="route('admin.sliders.update')">
        <div class="col-md-12 col-12 ">
            <x-input name="title" />
            <x-input-file name="image" />
            <x-input name="id" type="hidden"/>
        </div>

    </x-form>
</x-side-modal>
@endsection
@section('page-script')
<script>
    $(document).ready(function() {
        $(document).on('click', '[data-show]', function() {
            const modal = $(this).data('show');
            // $(`#${modal}`).modal('show');
            window.location.href = "{{route("admin.labours.add")}}"
        });
    });


    // $(document).on('click', function(){
    //     $('.drop-menuToggle').removeClass('active');
    // })

    function setValue(data, modal) {
        $(`${modal} #id`).val(data.id);
        $(`${modal} #title`).val(data.title);
        $(`${modal} #image`).val(data.image);
    
        $(modal).modal('show');
    }

</script>
@endsection
