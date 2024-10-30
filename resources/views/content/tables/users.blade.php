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

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection

@section('content')

    <section>
        <div class="row match-height">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <x-card>
                    <form action="{{route('admin.users.export')}}" method="GET">
                    <div class="d-flex justify-content-end align-items-center mb-1">
                         <div class="col-md-3">
                            <div class="form-group  text-left">
                                <label for="month">Date </label>
                                <input value="" type="text" class="form-control flatpickr flatpickr-input"
                                    required="" name="month" id="month" placeholder=" Enter Date"
                                    readonly="readonly">
                                <div class="invalid-tooltip">Please provide a valid Month</div>


                            </div>



                        </div>
                        <div class="col-md-3">
                    <!--<button class="btn mb-1 btn-primary text-end" href={{route('admin.users.export')}}>Export</a>-->
                    <button class="btn  btn-primary text-end">Export</a>
                    </div>
                    </div>
                    </form>
                    <livewire:user-table />
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
    <x-side-modal title="Update users" id="edit-user-modal">
        <x-form id="edit-user-modal" method="POST" class="" :route="route('admin.users.update')">
            <div class="col-md-12 col-12 ">
                <x-input name="name" />
                <x-input name="email" />
                <x-input name="id" type="hidden" />
                <x-input name="phone" type="number" />
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
            $(modal + ' #id').val(data.id);
            $(modal + ' #name').val(data.name);
             $(modal + ' #phone').val(data.phone);
             $(modal + ' #email').val(data.email);
            // $(modal + ' #phone').val(data.phone);
            // $(modal + ' #address').val(data.address);
            // $(modal + ' [name=gender][value=' + data.gender + ']').prop('checked', true).trigger('change');
            $(modal).modal('show');
        }
        
          $(".flatpickr").flatpickr(
            {
                mode: "range"
            }
        );
    </script>
@endsection
