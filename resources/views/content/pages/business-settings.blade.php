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
        <x-form id="add-ground" method="POST" :reset="0" class="" :route="route('admin.business-settings.store')">

            <x-divider text="Android Settings" />
            <div class="col-md-4">
                <x-input value="{{ $data['Android_Version'] ?? '' }}" name="Android_Version" label="Android Version" />
            </div>

            <div class="col-md-4 my-auto">
                <div class="custom-control custom-control-success custom-switch">
                    <input value="0" type="hidden" name="android_force_update">
                    <input value="1" name="android_force_update" type="checkbox"
                        @if ($data['android_force_update'] ?? '' == 1) checked @endif class="custom-control-input"
                        id="switch-force-update-android">
                    <label class="custom-control-label" for="switch-force-update-android">Force Update</label>
                </div>
            </div>

            <div class="col-md-4 my-auto">
                <div class="custom-control custom-control-success custom-switch">
                    <input value="0" type="hidden" name="android_maintenance_update">
                    <input value="1" name="android_maintenance_update" type="checkbox"
                        @if ($data['android_maintenance_update'] ?? '' == 1) checked @endif class="custom-control-input"
                        id="switch-android-maintenance-update">
                    <label class="custom-control-label" for="switch-android-maintenance-update">Maintenance Update</label>
                </div>
            </div>

            <div class="col-md-4">
                <x-input value="{{ $data['android_app_download'] ?? '' }}" name="android_app_download"
                    label="Android App Download Link" />
            </div>

            <x-divider text="IOS Settings" />

            <div class="col-md-4">
                <x-input value="{{ $data['IOS_Version'] ?? '' }}" name="IOS_Version" label="IOS Version" />
            </div>

            <div class="col-md-4 my-auto">
                <div class="custom-control custom-control-success custom-switch">
                    <input value="0" type="hidden" name="ios_force_update">
                    <input value="1" name="ios_force_update" type="checkbox"
                        @if ($data['ios_force_update'] ?? '' == 1) checked @endif class="custom-control-input"
                        id="switch-ios-force-updated">
                    <label class="custom-control-label" for="switch-ios-force-updated">Force Update</label>
                </div>
            </div>

            <div class="col-md-4 my-auto">
                <div class="custom-control custom-control-success custom-switch">
                    <input value="0" type="hidden" name="ios_maintenance_update">
                    <input value="1" name="ios_maintenance_update" type="checkbox"
                        @if ($data['ios_maintenance_update'] ?? '' == 1) checked @endif class="custom-control-input"
                        id="switch-ios-maintenance-update">
                    <label class="custom-control-label" for="switch-ios-maintenance-update">Maintenance Update</label>
                </div>
            </div>


            <div class="col-md-4 my-auto">
                <div class="custom-control custom-control-success custom-switch">
                    <input value="0" type="hidden" name="ios_production">
                    <input value="1" name="ios_production" type="checkbox"
                        @if ($data['ios_production'] ?? '' == 1) checked @endif class="custom-control-input"
                        id="switch-ios-production">
                    <label class="custom-control-label" for="switch-ios-production">IOS Production</label>
                </div>
            </div>

            <div class="col-md-4">
                <x-input value="{{ $data['ios_app_download'] ?? '' }}" name="ios_app_download"
                    label="IOS App Download Link" />
            </div>

            <!--<div class="col-md-3 my-auto">-->
            <!--    <div class="custom-control custom-control-success custom-switch">-->
            <!--        <input value="0" type="hidden" name="maintenance_mode">-->
            <!--        <input value="1" name="maintenance_mode" type="checkbox"-->
            <!--            @if ($data['maintenance_mode'] ?? '' == 1)
    checked
    @endif class="custom-control-input"-->
            <!--            id="switch-force-update-android">-->
            <!--        <label class="custom-control-label" for="switch-force-update-android">Maintenance Mode</label>-->
            <!--    </div>-->
            <!--</div>-->
            <!--<div class="col-md-3 my-auto">-->
            <!--    <div class="custom-control custom-control-success custom-switch">-->
            <!--        <input value="0" type="hidden" name="force_update_android">-->
            <!--        <input value="1" name="force_update_android" type="checkbox"-->
            <!--            @if ($data['force_update_android'] ?? '' == 1)
    checked
    @endif class="custom-control-input"-->
            <!--            id="switch-force-update-android">-->
            <!--        <label class="custom-control-label" for="switch-force-update-android">Force-Update Android</label>-->
            <!--    </div>-->
            <!--</div>-->
            <!--<div class="col-md-3 my-auto">-->
            <!--    <div class="custom-control custom-control-success custom-switch">-->
            <!--        <input value="0" type="hidden" name="force_update_ios">-->
            <!--        <input value="1" name="force_update_ios" type="checkbox"-->
            <!--            @if ($data['force_update_ios'] ?? '' == 1)
    checked
    @endif class="custom-control-input"-->
            <!--            id="switch-force-update-ios">-->
            <!--        <label class="custom-control-label" for="switch-force-update-ios">Force-Update IOS</label>-->
            <!--    </div>-->
            <!--</div>-->

            <x-divider text="Other Settings" />
            <div class="col-md-4 mt-3">

                <x-input name="razor_pay_key" value="{{ $data['razor_pay_key'] ?? '' }}" :required="false"
                    label="Razor Pay Key  " />
            </div>


            <div class="col-md-4 mt-3">

                <x-input name="service_charges" value="{{ $data['service_charges'] ?? '' }}" type="number"
                    label="Services Charges" />
            </div>

            <div class="col-md-4 mt-3">

                <x-input name="welcome_wallet_amount" value="{{ $data['welcome_wallet_amount'] ?? '' }}" type="number"
                    label="Welcome Wallet Amount" />
            </div>

            <div class="col-md-4 mt-3">

                <x-input name="referral_amount" value="{{ $data['referral_amount'] ?? '' }}" type="number"
                    label="Referral Amount" />
            </div>

            <div class="col-md-4 mt-3">

                <x-input name="referral_via_amount" value="{{ $data['referral_via_amount'] ?? '' }}" type="number"
                    label="Referral Via Amount" />
            </div>

            <div class="col-md-4 mt-3">

                <x-input name="minimum_withdrawal" value="{{ $data['minimum_withdrawal'] ?? '' }}" type="number"
                    label="Minimum Withdrawal" />
            </div>
            <div class="col-md-4 mt-3">
                <x-input name="gst" value="{{ $data['gst'] ?? '' }}" type="number" label="GST" />
            </div>

            {{-- <div class="col-md-4 mt-3">
                <x-input name="percentage_for_less_than" value="{{ $data['percentage_for_less_than'] ?? '' }}"
                    type="number" label="Percentage for Less Than 12 Hours" />
            </div>
            <div class="col-md-4 mt-3">

                <x-input name="percentage_for_less_than" value="{{ $data['percentage_for_less_than'] ?? '' }}"
                    type="number" label="Percentage for More than 12 Hours" />
            </div> --}}

            <div class="col-md-4 mt-3">

                <x-input name="radius" value="{{ $data['radius'] ?? '' }}" type="number" label="Radius" />
            </div>
            <div class="col-md-12 mt-3">
                <label for="about_us">About Us</label>
                <x-editor name="about_us" />
            </div>
            <div class="col-md-12 mt-3">
                <label for="contact_us">Contact Us</label>
                <x-editor name="contact_us" />
            </div>

            <div class="col-md-12 mt-5">

                <x-editor name="privacy_policy" label="Privacy Policy" />
            </div>

            <div class="col-md-12 mt-5">
                <x-editor name="terms_and_conditions" id="terms_and_conditions" label="Terms and Condition" />


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

            fullEditor_privacy_policy.root.innerHTML =
                @if ($data['privacy_policy'] ?? '' != '')
                    `{!! $data['privacy_policy'] ?? '' !!}`
                @endif

            fullEditor_terms_and_conditions.root.innerHTML =
                `{!! $data['terms_and_conditions'] ?? '' !!}`


        });
    </script>
@endsection
