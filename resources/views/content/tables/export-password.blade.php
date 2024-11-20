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
                    <x-form id="add-exportpassword" method="POST" class="" :route="route('admin.users.saveExportPassword')">
                        <div class="col-lg-3">
                            <x-input name="new_password" type="password"></x-input>
                            <x-input name="confirm_password" type="password"></x-input>

                        </div>
                    </x-form>
                </x-card>
            </div>
        </div>
    </section>


@endsection
@section('page-script')
    <script></script>
@endsection
