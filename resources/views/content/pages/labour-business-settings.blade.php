@extends('layouts/contentLayoutMaster')

@section('title', 'Business Settings')
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
    <x-card>
        <x-form id="add-ground" method="POST" :reset="0" class="" :route="route('admin.labour-business-settings.store')">
            <div class="col-md-3">
                <x-input value="{{ $data['android_version'] ?? '' }}" name="android_version" />
            </div>
            {{-- <div class="col-md-3">
                <x-input value="{{ $data['key2'] ?? '' }}" name="key2" />
            </div> --}}
            <div class="col-md-3 my-auto">
                <div class="custom-control custom-control-success custom-switch">
                    <input value="0" type="hidden" name="maintenance_mode">
                    <input value="1" name="maintenance_mode" type="checkbox"
                        @if ($data['maintenance_mode'] ?? '' == 1) checked @endif class="custom-control-input"
                        id="switch-force-update-android">
                    <label class="custom-control-label" for="switch-force-update-android">Maintenance Mode</label>
                </div>
            </div>
            <div class="col-md-3 my-auto">
                <div class="custom-control custom-control-success custom-switch">
                    <input value="0" type="hidden" name="force_update_android">
                    <input value="1" name="force_update_android" type="checkbox"
                        @if ($data['force_update_android'] ?? '' == 1) checked @endif class="custom-control-input"
                        id="switch-force-update-android">
                    <label class="custom-control-label" for="switch-force-update-android">Force-Update Android</label>
                </div>
            </div>
            <div class="col-md-3 my-auto">
                <div class="custom-control custom-control-success custom-switch">
                    <input value="0" type="hidden" name="force_update_ios">
                    <input value="1" name="force_update_ios" type="checkbox"
                        @if ($data['force_update_ios'] ?? '' == 1) checked @endif class="custom-control-input"
                        id="switch-force-update-ios">
                    <label class="custom-control-label" for="switch-force-update-ios">Force-Update IOS</label>
                </div>
            </div>

            {{-- <div class="col-md-6 mt-3">
           
                <x-input name="razor_pay_key" value="{{ $data['razor_pay_key'] ?? '' }}" :required="false" label="Razor Pay Key  " />
            </div> --}}


            {{-- <div class="col-md-6 mt-3">
           
                <x-input name="service_charges" value="{{ $data['service_charges'] ?? '' }}" type="number" label="Services Charges" />
            </div> --}}
            <div class="col-md-12 mt-3">
                <label for="about_us">About Us</label>
                <x-editor name="about_us" />
            </div>
            <div class="col-md-12 mt-3">
                <label for="contact_us">Contact Us</label>
                <x-editor name="contact_us" />
            </div>
            <div class="col-md-4 mt-3">
                <x-input value="{{ $data['privacy_policy'] ?? '' }}" name="privacy_policy" />
            </div>
            <div class="col-md-4 mt-3">
                <x-input value="{{ $data['terms_and_conditions'] ?? '' }}" name="terms_and_conditions" />
            </div>

            <div class="col-md-4 mt-3">
                <x-input value="{{ $data['download_link'] ?? '' }}" name="download_link" label="App Download Link" />
            </div>

            <div class="col-md-12 mt-3">
                <x-input value="{{ $data['share_content'] ?? '' }}" name="share_content" type="textarea" />
            </div>
        </x-form>
    </x-card>
@endsection
@section('page-script')
    <script>
        $(function() {
            fullEditor_about_us.root.innerHTML =
                @if ($data['about_us'] ?? '' != '')
                    `{!! $data['about_us'] ?? '' !!}`
                @endif
            fullEditor_contact_us.root.innerHTML =
                @if ($data['contact_us'] ?? '' != '')
                    `{!! $data['contact_us'] ?? '' !!}`
                @endif
           
            fullEditor_terms_and_condition.root.innerHTML =
                @if ($data['terms_and_condition'] ?? '' != '')
                    `{!! $data['terms_and_condition'] ?? '' !!}`
                @endif
        })
    </script>
@endsection
