@extends('layouts/contentLayoutMaster')

@section('title', 'Student')
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
                {{-- {!! $dataTable->table() !!} --}}

                <livewire:student-table />

            </x-card>
        </div>
    </div>
</section>


<x-side-modal title="Add student" id="add-blade-modal">
    <x-form id="add-student" method="POST" class="" :route="route('admin.students.store')">
        <div class="col-md-12 col-12 ">
            <x-input name="name" />
            <x-input name="rollno" />
            <x-input-file name="image" />
            <x-input name="city" />
            <x-input name="state" />
        </div>
    </x-form>
</x-side-modal>
<x-side-modal title="Update student" id="edit-student-modal">
    <x-form id="edit-student" method="POST" class="" :route="route('admin.students.update')">

        <div class="col-md-12 col-12 ">
            <x-input name="name" />
            <x-input name="rollno" />
            <x-input-file name="image" />
            <x-input name="city" />
            <x-input name="state" />
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
        $(`${modal} #name`).val(data.name);
        $(`${modal} #rollno`).val(data.rollno);
        $(`${modal} #city`).val(data.city);
        $(`${modal} #state`).val(data.state);
        $(modal).modal('show');
    }

</script>
@endsection
