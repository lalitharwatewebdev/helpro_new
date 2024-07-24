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

    <section id="dashboard-card">
        <div class="row match-height">
            <div onclick="location.href='{{ route('admin.home.index') }}'" class="col-lg-4 col-sm-6 col-12">
                <div class="card cursor-pointer">
                    <div class="card-header">
                        <div>
                            <h2 class="font-weight-bolder mb-0">{{ $user_count ?? 0 }}</h2>
                            <h6 class="card-text">Total Users</h6>
                        </div>
                        <div class="avatar bg-light-primary p-50 m-0">
                            <div class="avatar-content">
                                <i data-feather='truck'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div onclick="location.href='{{ route('admin.home.index') }}?table[filters][status]=active&table[filters][approval_status]='"
                class="col-lg-4 col-sm-6 col-12">
                <div class="card cursor-pointer">
                    <div class="card-header">
                        <div>
                            <h2 class="font-weight-bolder mb-0">{{ $product_count ?? 0 }}</h2>
                            <h6 class="card-text">Total Products</h6>
                        </div>
                        <div class="avatar bg-light-primary p-50 m-0">
                            <div class="avatar-content">
                                <i data-feather='cpu'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('page-script')
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
