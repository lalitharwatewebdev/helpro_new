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

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection

@section('content')


    <section>
        <div class="row match-height">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <x-card>
                    <livewire:labour-redeem-table/>
                </x-card>
            </div>
        </div>
    </section>


   
@endsection
@section('page-script')
    <script>
        $(document).ready(function() {
            $(document).on('click', '[data-show]', function() {
                const modal = $(this).data('show');
                // $(`#${modal}`).modal('show');
                window.location.href = "{{ route('admin.labours.add') }}"
            });
        });


        // $(document).on('click', function(){
        //     $('.drop-menuToggle').removeClass('active');
        // })

        function setValue(data, modal) {
            // $(`${modal} #id`).val(data.id);
            // $(`${modal} #title`).val(data.title);
            // $(`${modal} #image`).val(data.image);

            $(modal).modal('show');
        }
    </script>
@endsection
