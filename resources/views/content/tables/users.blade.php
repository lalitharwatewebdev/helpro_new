@extends('layouts/contentLayoutMaster')

@section('title', 'Users')
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
                    {!! $dataTable->table() !!}
                </x-card>
            </div>
        </div>
    </section>
    <x-side-modal title="Add users" id="add-users-modal">
        <x-form id="add-users" method="POST" class="" :route="route('admin.users.store')">
            <div class="col-md-12 col-12 ">
                <x-input name="name" />
            </div>
            <div class="col-md-12 col-12 ">
                <x-input name="phone" type="number" />
            </div>
        </x-form>
    </x-side-modal>
    <x-side-modal title="Update users" id="edit-users-modal">
        <x-form id="edit-users" method="POST" class="" :route="route('admin.users.update')">
            <div class="col-md-12 col-12 ">
                <x-input name="name" />
                <x-input name="id" type="hidden" />
            </div>
            <div class="col-md-12 col-12 ">
                <x-input name="phone" />
            </div>
        </x-form>
    </x-side-modal>
@endsection
@section('page-script')
    <script>
        $(document).ready(function() {
            // $('#users-table_wrapper .dt-buttons').append(
            //     `<button type="button" data-show="add-users-modal" class="btn btn-flat-success border border-success waves-effect float-md-right">Add</button>`
            // );
            // $(document).on('click', '[data-show]', function() {
            //     const modal = $(this).data('show');
            //     $(`#${modal}`).modal('show');
            // });
        });

        function setValue(data, modal) {
            console.log(data);
            // $(modal + ' #id').val(data.id);
            // $(modal + ' #name').val(data.name);
            // $(modal + ' #phone').val(data.phone);
            // $(modal + ' #email').val(data.email);
            // $(modal + ' #phone').val(data.phone);
            // $(modal + ' #address').val(data.address);
            // $(modal + ' [name=gender][value=' + data.gender + ']').prop('checked', true).trigger('change');
            // $(modal).modal('show');
        }
    </script>
@endsection
