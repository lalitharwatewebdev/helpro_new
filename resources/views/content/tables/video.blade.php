@extends('layouts/contentLayoutMaster')

@section('title', 'Video')
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
                    <livewire:video-table />
                </x-card>
            </div>
        </div>
    </section>


    <x-side-modal title="Add Videos" id="add-blade-modal">
        <x-form id="add-category" method="POST" class="" :route="route('admin.videos.store')">
            <div class="col-md-12 col-12 ">
                <x-input name="title" />
                <x-input name="video" />
                <div class="mb-1">
                    <label for="">Video Type</label>
                    <select name="video_type" class="select2  form-control" id="">
                        <option value="" selected>Select Video Type</option>
                        <option value="labour">Labour</option>
                        <option value="user">User</option>
                    </select>
                </div>
                <x-input-file name="image" />
            </div>
        </x-form>
    </x-side-modal>

    <x-side-modal title="Update Video" id="edit-video-modal">
        <x-form id="edit-subscription-modal" method="POST" class="" :route="route('admin.videos.update')">
            <div class="col-md-12 col-12 ">
                <x-input name="title" />
                <x-input name="video" />
                <div class="mb-1">
                    <label for="">Video Type</label>
                    <select name="video_type" id="video_type" class="select2  form-control" id="">
                        <option value="" selected>Select Video Type</option>
                        <option value="labour">Labour</option>
                        <option value="user">User</option>
                    </select>
                </div>
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
            $(`${modal} #title`).val(data.title);
            $(`${modal} #video`).val(data.video);
            $(`${modal} #video_type`).val(data.video_type).trigger("change");
            $(modal).modal('show');
        }
    </script>
@endsection
