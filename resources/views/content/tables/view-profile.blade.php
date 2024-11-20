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
                    <h2 class="text-center font-weight-bolder text-primary">
                        {{ $user->type == "labour" ? "Labour Profile" : "User Profile" }}
                    </h2>
                
                    <div class="d-flex justify-content-center">
                        <img src="{{ asset($user->profile_pic ?? 'images/placeholder.jpg') }}" width="100px" class="view-on-click rounded-circle" alt="Profile Picture">
                    </div>
                    
                    <div class="row mt-2">
                        <div class="col-lg-4 mb-4">
                            <p>Name: {{ $user->name }}</p>
                        </div>
                        <div class="col-lg-4 mb-4">
                            <p>Email: {{ $user->email ?? "" }}</p>
                        </div>
                        <div class="col-lg-4 mb-4">
                            <p>Phone: {{ $user->phone ?? "" }}</p>
                        </div>
                        <div class="col-lg-4 mb-4">
                            <p>State: {{ $user->states->name ?? "" }}</p>
                        </div>
                        <div class="col-lg-4 mb-4">
                            <p>City: {{ $user->cities->name ?? "" }}</p>
                        </div>
                
                        @if($user->type == "labour")
                            <div class="col-lg-4 mb-4">
                                <p>Aadhaar Number: {{ $user->aadhaar_number }}</p>
                            </div>
                            <div class="col-lg-4 mb-4">
                                <p>Aadhaar Card Front:</p>
                                <img src="{{ asset($user->aadhaar_card_front) }}" width="100px" class="view-on-click rounded-circle" alt="Aadhaar Card Front">
                            </div>
                            <div class="col-lg-4 mb-4">
                                <p>Aadhaar Card Back:</p>
                                <img src="{{ asset($user->aadhaar_card_back) }}" width="100px" class="view-on-click rounded-circle" alt="Aadhaar Card Back">
                            </div>
                       
                
                     
                            <div class="col-lg-4 mb-4">
                                <p>Pan Card Number: {{ $user->pan_card_number ?? "" }}</p>
                            </div>
                            <div class="col-lg-4 mb-4">
                                <p>Pan Card Front:</p>
                                <img src="{{ asset($user->pan_front) }}" width="100px" class="view-on-click rounded-circle" alt="Pan Card Front">
                            </div>
                       
                
                        <div class="col-lg-4 mb-4">
                            <p>Bank Name: {{ $user->bank_name ?? "" }}</p>
                        </div>
                        <div class="col-lg-4 mb-4">
                            <p>Account Number: {{ $user->account_number ?? "" }}</p>
                        </div>
                        <div class="col-lg-4 mb-4">
                            <p>IFSC Code: {{ $user->IFSC_code ?? "" }}</p>
                        </div>
                        <div class="col-lg-4 mb-4">
                            <p>Branch Address: {{ $user->branch_address ?? "" }}</p>
                        </div>
                        <x-card>
    <h2 class="text-center font-weight-bolder text-primary">
        {{ $user->type == "labour" ? "Labour Profile" : "User Profile" }}
    </h2>

    <div class="d-flex justify-content-center">
        <img src="{{ asset($user->profile_pic ?? 'images/placeholder.jpg') }}" width="100px" class="view-on-click rounded-circle" alt="Profile Picture">
    </div>
    
    <div class="row mt-2">
        <div class="col-lg-4 mb-4">
            <p>Name: {{ $user->name }}</p>
        </div>
        <div class="col-lg-4 mb-4">
            <p>Email: {{ $user->email ?? "" }}</p>
        </div>
        <div class="col-lg-4 mb-4">
            <p>Phone: {{ $user->phone ?? "" }}</p>
        </div>
        <div class="col-lg-4 mb-4">
            <p>State: {{ $user->states->name ?? "" }}</p>
        </div>
        <div class="col-lg-4 mb-4">
            <p>City: {{ $user->cities->name ?? "" }}</p>
        </div>

        @if($user->type == "labour")
            <div class="col-lg-4 mb-4">
                <p>Aadhaar Number: {{ $user->aadhaar_number }}</p>
            </div>
            <div class="col-lg-4 mb-4">
                <p>Aadhaar Card Front:</p>
                <img src="{{ asset($user->aadhaar_card_front) }}" width="100px" class="view-on-click rounded-circle" alt="Aadhaar Card Front">
            </div>
            <div class="col-lg-4 mb-4">
                <p>Aadhaar Card Back:</p>
                <img src="{{ asset($user->aadhaar_card_back) }}" width="100px" class="view-on-click rounded-circle" alt="Aadhaar Card Back">
            </div>
        @endif

        @if($user->type == "user")
            <div class="col-lg-4 mb-4">
                <p>Pan Card Number: {{ $user->pan_card_number ?? "" }}</p>
            </div>
            <div class="col-lg-4 mb-4">
                <p>Pan Card Front:</p>
                <img src="{{ asset($user->pan_front) }}" width="100px" class="view-on-click rounded-circle" alt="Pan Card Front">
            </div>
        @endif

        <div class="col-lg-4 mb-4">
            <p>Bank Name: {{ $user->bank_name ?? "" }}</p>
        </div>
        <div class="col-lg-4 mb-4">
            <p>Account Number: {{ $user->account_number ?? "" }}</p>
        </div>
        <div class="col-lg-4 mb-4">
            <p>IFSC Code: {{ $user->IFSC_code ?? "" }}</p>
        </div>
        <div class="col-lg-4 mb-4">
            <p>Branch Address: {{ $user->branch_address ?? "" }}</p>
        </div>
        @endif
        <div class="col-lg-4 mb-4">
            <p>Address: {{ $user->address ?? "" }}</p>
        </div>
        <div class="col-lg-4 mb-4">
            <p>Gender: {{ $user->gender ?? "" }}</p>
        </div>
        <div class="col-lg-4 mb-4">
            <p>Lat Long: {{ $user->lat_long ?? "" }}</p>
        </div>
    </div>
</x-card>

                        <div class="col-lg-4 mb-4">
                            <p>Address: {{ $user->address ?? "" }}</p>
                        </div>
                        <div class="col-lg-4 mb-4">
                            <p>Gender: {{ $user->gender ?? "" }}</p>
                        </div>
                        <div class="col-lg-4 mb-4">
                            <p>Lat Long: {{ $user->lat_long ?? "" }}</p>
                        </div>
                    </div>
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
                $(`#${modal}`).modal('show');
            });
        });


        // $(document).on('click', function(){
        //     $('.drop-menuToggle').removeClass('active');
        // })

        function setValue(data, modal) {
            $(`${modal} #id`).val(data.id);
            $(`${modal} #title`).val(data.title);
            $(`${modal} #amount`).val(data.amount);
            $(`${modal} #days`).val(data.days);
            $(modal).modal('show');
        }
    </script>
@endsection
