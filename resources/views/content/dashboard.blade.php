@extends('layouts/contentLayoutMaster')

@section('title', 'Dashboard Analytics')
@section('page-style')
    <style>
        .avatar svg {
            height: 20px;
            width: 20px;
            font-size: 1.45rem;
            flex-shrink: 0;
        }

        .dark-layout .avatar svg {
            color: #fff;
        }

        .cursor {
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    <section class="row">
        <div class="col-lg-6">
            <div class="card" id="chart">
                
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card" id="labour">
                
            </div>
        </div>
    </section>
    <section id="dashboard-card">
        <div class="row match-height">
            <div onclick="location.href='{{ route('admin.users.index') }}'" class="col-lg-3 col-sm-6 col-12">
                <div class="card cursor-pointer">
                    <div class="card-header">
                        <div>
                            <h2 class="font-weight-bolder mb-0">{{ $users ?? 0 }}</h2>
                            <h6 class="card-text">Total Users</h6>
                        </div>
                        <div class="avatar bg-light-primary p-50 m-0">
                            <div class="avatar-content">
                                <span class="material-symbols-outlined">
                                    account_circle
                                    </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div onclick="location.href='{{ route('admin.labours.pending') }}?table[filters][status]=active&table[filters][approval_status]='"
                class="col-lg-3 col-sm-6 col-12">
                <div class="card cursor-pointer">
                    <div class="card-header">
                        <div>
                            <h2 class="font-weight-bolder mb-0">{{ $labours ?? 0 }}</h2>
                            <h6 class="card-text">Total Labours</h6>
                        </div>
                        <div class="avatar bg-light-primary p-50 m-0">
                            <div class="avatar-content">
                                <span class="material-symbols-outlined">
                                    diversity_3
                                    </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div onclick="location.href='{{ route('admin.sliders.user') }}?table[filters][status]=active&table[filters][approval_status]='"
            class="col-lg-3 col-sm-6 col-12">
            <div class="card cursor-pointer">
                <div class="card-header">
                    <div>
                        <h2 class="font-weight-bolder mb-0">{{ $user_slider ?? 0 }}</h2>
                        <h6 class="card-text">Total User Slider</h6>
                    </div>
                    <div class="avatar bg-light-primary p-50 m-0">
                        <div class="avatar-content">
                            <span class="material-symbols-outlined">
                                filter_frames
                                </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div onclick="location.href='{{ route('admin.sliders.labour') }}'" class="col-lg-3 col-sm-6 col-12">
            <div class="card cursor-pointer">
                <div class="card-header">
                    <div>
                        <h2 class="font-weight-bolder mb-0">{{ $labour_slider ?? 0 }}</h2>
                        <h6 class="card-text">Total Labour Slider</h6>
                    </div>
                    <div class="avatar bg-light-primary p-50 m-0">
                        <div class="avatar-content">
                            <span class="material-symbols-outlined">
                                filter_frames
                                </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div onclick="location.href='{{ route('admin.category.index') }}'" class="col-lg-3 col-sm-6 col-12">
            <div class="card cursor-pointer">
                <div class="card-header">
                    <div>
                        <h2 class="font-weight-bolder mb-0">{{ $total_category ?? 0 }}</h2>
                        <h6 class="card-text">Total Categories</h6>
                    </div>
                    <div class="avatar bg-light-primary p-50 m-0">
                        <div class="avatar-content">
                            <span class="material-symbols-outlined">
                                category
                                </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div onclick="location.href='{{ route('admin.subscriptions.index') }}'" class="col-lg-3 col-sm-6 col-12">
            <div class="card cursor-pointer">
                <div class="card-header">
                    <div>
                        <h2 class="font-weight-bolder mb-0">{{ $subscriptions ?? 0 }}</h2>
                        <h6 class="card-text">Total Subscriptions</h6>
                    </div>
                    <div class="avatar bg-light-primary p-50 m-0">
                        <div class="avatar-content">
                            <span class="material-symbols-outlined">
                                category
                                </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div onclick="location.href='{{ route('admin.business-settings.index') }}'" class="col-lg-3 col-sm-6 col-12">
            <div class="card cursor-pointer">
                <div class="card-header">
                    <div>
                        <h2 class="font-weight-bolder mb-0">{{ $business_settings['service_charges'] ?? 0 }}</h2>
                        <h6 class="card-text">Service Charges</h6>
                    </div>
                    <div class="avatar bg-light-primary p-50 m-0">
                        <div class="avatar-content">
                            <span class="material-symbols-outlined">
                                category
                                </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div onclick="location.href='{{ route('admin.promo-code.index') }}'" class="col-lg-3 col-sm-6 col-12">
            <div class="card cursor-pointer">
                <div class="card-header">
                    <div>
                        <h2 class="font-weight-bolder mb-0">{{ $promo_code ?? 0 }}</h2>
                        <h6 class="card-text">Total Promo Code</h6>
                    </div>
                    <div class="avatar bg-light-primary p-50 m-0">
                        <div class="avatar-content">
                            <span class="material-symbols-outlined">
                                category
                                </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
@endsection

@section('page-script')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    var options = {
  chart: {
    type: 'line'
  },
  colors:['#de1058', '#de1058', '#de1058'],
  series: [{
    name: 'users',
    data: @json($user_count)
  }],
  xaxis: {
    categories: @json($user_date)
  },
  stroke: {
    curve: 'smooth',
  },
  title: {
    text: 'Users',
    align: 'left'
  }
}

var chart = new ApexCharts(document.querySelector("#chart"), options);

chart.render();
</script>

<script>
    var labour = {
  chart: {
    type: 'line'
  },
  colors:['#de1058', '#de1058', '#de1058'],
  stroke: {
    curve: 'smooth',
  },
  dropShadow: {
        enabled: false,
        enabledOnSeries: undefined,
        top: 0,
        left: 0,
        blur: 3,
        color: '#000',
        opacity: 0.35
    },
  series: [{
    name: 'labours',
    data: @json($labour_count)
  }],
  xaxis: {
    categories: @json($labour_date)
  },
  title: {
    text: 'Labours',
    align: 'left'
  }
}

var chart = new ApexCharts(document.querySelector("#labour"), labour);

chart.render();
</script>

    {{-- english to hindi translation API integration --}}
    {{-- <script>
        $(function() {
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                }
            });
            $.ajax({
                type: "post",
                url: "https://translo.p.rapidapi.com/api/v3/translate",
                headers: {
                    "content-type": "application/x-www-form-urlencoded",
                    "X-RapidAPI-Key": "8daa8ba644msh3ac8f6098c5df4ap13bd73jsn0b2cafc54a62",
                    "X-RapidAPI-Host": "translo.p.rapidapi.com"
                },
                data: {
                    "from": "en",
                    "to": "hi",
                    "text": "delivery"
                },
                success: function(response) {
                    console.log(response);
                }
            });
        })
    </script> --}}
@endsection
