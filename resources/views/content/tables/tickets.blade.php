@extends('layouts/contentLayoutMaster')

@section('title', 'Areas')
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
                    @foreach ($tickets as $ticket)
                        <div>
                            <p>
                                <a class="btn btn-primary ticket-btn" data-toggle="collapse" 
                                    href="#collapseExample{{ $ticket->id }}" role="button" aria-expanded="false"
                                    data-val="{{ $ticket->id }}" aria-controls="collapseExample">
                                    {{ $ticket->ticket_name }}
                                </a> <span style="font-size:10px">{{ $ticket->ticket_number }}</span>

                            </p>
                            <div class="collapse" id="collapseExample{{ $ticket->id }}">
                                <div class="card card-body{{$ticket->id}}">
                                   
                                </div>
                                <span  data-id="{{$ticket->id}}" class="mb-4 btn btn-outline-primary reply-btn">Reply</span>
                            </div>
                        </div>
                    @endforeach
    {{-- 
                        {{ $tickets->links() }} --}}
                </x-card>
            </div>
        </div>
    </section>


    {{-- <x-side-modal title="Add Cart" id="add-blade-modal">
        <x-form id="add-category" method="POST" class="" :route="route('admin.carts.store')">
            <div class="col-md-12 col-12 ">
                <x-input name="title" />
                <x-input-file name="image" />
            </div>
        </x-form>
    </x-side-modal> --}}
    <x-side-modal title="Update Cart" id="add-blade-modal">
        <x-form id="edit-category-modal" method="POST" class="" :route="route('admin.tickets.store')">
            <div class="col-md-12 col-12 ">
                <x-input name="message" />
                {{-- <x-input-file name="image" /> --}}
                <x-input name="id" type="hidden" class="message-id" />
            </div>

        </x-form>
    </x-side-modal>
@endsection
@section('page-script')
    <script>
        $(document).ready(function() {
            $(document).on('click', '[data-show]', function() {
                // const modal = $(this).data('show');
                // window.location.href = "{{ route('admin.areas.add-areas') }}"
            });

            $(".ticket-btn").on("click", function(e) {
                e.preventDefault()
                let ticketId = $(this).data('val');

                $.ajax({
                    method: "GET",
                    url: "{{ route('admin.tickets.get-ticket-by-id') }}",
                    data: {
                        ticket_id: ticketId
                    },
                    success: function(response) {
                        // var ticketId = $(this).data('val');
                        var cardBody = '.card-body'+ticketId;
                       
                        
                        $(cardBody).empty();

                       
                        if (response.data.length === 0) {
                            $(cardBody).append(
                            `<p>No messages available.</p>`);
                        } else {
                            
                            response.data.forEach(function(item) {
                                if(item.isAdmin == "0"){
                                    
                                    $(cardBody).append(
                                    `<p class="px-2 py-2 pl-0 shadow">${item.message}</p>
                                   
                                    `)
                                }
                                else{
                                    $(cardBody).append(
                                    `<p class="px-2 py-2 pl-0 shadow">${item.message} <span class="text-primary" style="font-size:10px">(Admin)</span></p>
                                    `)
                                }
                            });
                        }

                       
                    },

                    error: function(error) {
                        // Handle error
                        console.error("Error:", textStatus, errorThrown);
                    }
                })
            })

            $(".reply-btn").on("click",function(){
               var id =  $(this).data('id');
                $("#add-blade-modal").modal("show")
                $("#add-blade-modal").find(".message-id").val(id)

            
            })
            
        });


        function setValue(data, modal) {
           
        }
    </script>
@endsection
