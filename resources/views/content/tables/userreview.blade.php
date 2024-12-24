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
                    <table class="table table-bordered">
                        <tr>
                            <th>User Name</th>
                            <th>Review</th>
                            <th>Rating</th>

                        </tr>
                        @if (!empty($labour_data))

                            @forelse ($labour_data as $key=>$val)
                                <tr>
                                    <td>{{ $val->user['name'] ?? '' }}</td>
                                    <td>{{ $val['review'] ?? '' }}</td>
                                    <td>{{ $val['rating'] ?? '' }}</td>
                                </tr>
                            @empty
                            @endforelse
                        @endif
                    </table>
                </x-card>
            </div>
        </div>
    </section>
@endsection
@section('page-script')
    <script></script>
@endsection
