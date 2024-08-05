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
                    <x-divider text="User Details" />
                    <div class="row">
                        <div class="col-lg-4 col-md-6">
                            <span> <b>Name:</b> {{$data->name}} </span>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <span> <b>Email:</b> {{$data->email}} </span>
                        </div>  

                        <div class="col-lg-4 col-md-6">
                            <span> <b>Email:</b> {{$data->phone}} </span>
                        </div>  
                    </div>
                </x-card>
            </div>
    </section>

@endsection
@pushonce('component-script')
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
@endpushonce
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
            // console.log(data);
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

    <script>
        $(document).ready(function() {
            $(".state-select").on("change", function() {
                let state_id = $(".state-select").val()

                $.ajax({
                    type: "GET",
                    data: {
                        papi
                        state_id: state_id
                    },
                    url: '{{ route('admin.labours.city') }}',
                    success: function(response) {

                        $(".city-select").empty()

                        $(".city-select").append(
                            `<option value="" selected disabled>Select City</option>`)

                        response.forEach((data) => {
                            $(".city-select").append(`
            <option value="${data.id}">${data.name}</option>
        `);
                        });
                    }
                })
            })
        })
    </script>

    <script>
        $(document).ready(function() {
            $(".start_time").on("change", function() {
                let start_time = $(this).val();

            });


            $(".end_time").on("change", function() {
                let end_time = $(this).val();
            });







        })
    </script>

    <script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>

    <script src="https://unpkg.com/jquery-filepond/filepond.jquery.js"></script>

    <script>
        $('.my-pond').filepond();
    </script>
@endsection
