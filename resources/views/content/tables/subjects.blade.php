@extends('layouts/contentLayoutMaster')

@section('title', 'Subject')
@section('page-style')
<style>
    [x-cloak] { display: none !important; }
    .dropdown-menu {
    transform: scale(1)!important;
}
</style>
@endsection

@section('content')

    <section>
        <div class="row match-height">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <x-card>
                    <livewire:subject-table />
                </x-card>
            </div>
        </div>
    </section>


    <x-side-modal title="Add subject" id="add-blade-modal">
        <x-form id="add-subject" method="POST" class="" :route="route('admin.subjects.store')">
            <div class="col-md-12 col-12 ">
                <x-input name="name" />
                <x-select name="student_id" :options="$students" />
            </div>
        </x-form>
    </x-side-modal>
    <x-side-modal title="Update subject" id="edit-subject-modal">
        <x-form id="edit-subject" method="POST" class="" :route="route('admin.subjects.update')">

            <div class="col-md-12 col-12 ">
                <x-input name="name" />
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

        function setValue(data, modal) {

            $(`${modal} #id`).val(data.id);
            $(`${modal} #name`).val(data.name);
            $(modal).modal('show');
        }
    </script>
@endsection
