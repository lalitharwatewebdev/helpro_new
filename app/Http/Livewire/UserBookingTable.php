<?php
namespace App\Http\Livewire;

use App\Exports\CustomExport;
use App\Models\Booking;
use App\Models\LabourAcceptedBooking;
use Excel;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class UserBookingTable extends DataTableComponent
{

    protected $model = Booking::class;
    public $counter = 1;
    public $user_id;
    public $type;
    public function mount($type)
    {
        // $this->user_id = $user_id;
        $this->type = $type;

        $this->dispatchBrowserEvent('table-refreshed');
    }

    public function configure(): void
    {
        $this->counter = 1;
        $this->setFilterPillsStatus(false);

        $this->setFiltersDisabled();
        $this->setBulkActionsDisabled();
        $this->setColumnSelectDisabled();

        $this->setPrimaryKey('id')

            ->setDefaultSort('id', 'desc')
            ->setEmptyMessage('No Result Found')
            ->setTableAttributes([
                'id' => 'category-table',
            ])
            ->setBulkActions([
                'exportSelected' => 'Export',
            ])
            ->setConfigurableAreas([
                // 'toolbar-right-end' => 'content.rapasoft.add-button',
                // 'toolbar-left-end' => [
                //     'content.rapasoft.active-inactive',
                //     [
                //         'route' => 'admin.category.index',
                //     ]
                // ]
            ]);
    }

    public function columns(): array
    {
        return [

            Column::make('SrNo.', 'id')
                ->format(function ($value, $row, Column $column) {
                    return (($this->page - 1) * $this->getPerPage()) + ($this->counter++);
                })
                ->html(),
            Column::make('Status')
                ->label(function ($row, Column $column) {
                    return "<select class='form-control' data-id='" . $row->id . "' name='booking_status' id='booking_status'>
                    <option value=''>Select Status</option>
                    <option value='accepted'>Accepted</option>
                    <option value='rejected'>Rejected</option>
                    </select>";
                })->html(),

            Column::make('Cancel Booking')
                ->label(function ($row, Column $column) {
                    // return $row->checkout->start_date;

                    if ($row->checkout->start_date == date('Y-m-d', strtotime(now()))) {
                        return "<a href='#' data-day='today' data-orderid = '" . $row->id . "' class='btn btn-primary cancel_booking'>Cancel</a>";
                    } else {
                        return "<a href='#' data-day='nottoday'  data-orderid = '" . $row->id . "' class='btn btn-primary cancel_booking'>Cancel</a>";
                    }

                })->html(),

            Column::make("labour_booking_id")
                ->collapseOnTablet()
                ->searchable()
                ->sortable(),
            // Column::make("Labour Name", 'labour_id')
            //     ->format(function ($value, $row, Column $column) {
            //         return $row->labour->name ?? "";
            //     })
            //     ->html(),
            Column::make('User Review')
                ->label(function ($row, Column $column) {
                    $edit_route = route('admin.userbookings.userreview', ['id' => $row->id]);
                    return '<a class="btn btn-primary" id="accepted_labour_list"  href=' . $edit_route . '>Reviews</a>';
                })->html(),
            Column::make('Labour Accepted Count')
                ->label(function ($row, Column $column) {
                    $labour_accepted_booking = LabourAcceptedBooking::where('booking_id', $row->labour_booking_id)->count();
                    $edit_route = route('admin.userbookings.labourlist', ['labour_booking_id' => $row->labour_booking_id]);
                    return '<a class="btn btn-primary" id="accepted_labour_list"  href=' . $edit_route . '>' . $labour_accepted_booking . '</a>';
                })->html(),
            Column::make("User Details", 'user_id')
                ->format(function ($value, $row, Column $column) {
                    $edit = route('admin.tickets.profile', ['id' => $row->user_id]);
                    return '<a class="text-end labour-profile"
                                href="' . $edit . '">
                                <span class="material-symbols-outlined">person</span>
                            </a>';
                    // return $row->user->name ?? "";
                })
                ->html(),
            Column::make("User", 'user_id')
                ->format(function ($value, $row, Column $column) {
                    return $row->user->name ?? "";
                })
                ->html(),
            Column::make('Category')
                ->label(function ($row, Column $column) {
                    $data = Booking::with(['checkout' => ['category']])->where('id', $row->id)->first();
                    return $data->checkout->category->title ?? '';
                }),
            Column::make("Total Amount", 'total_amount')
                ->format(function ($value, $row, Column $column) {
                    return $value;
                })
                ->html(),
            Column::make("Quantity", 'quantity_required')
                ->format(function ($value, $row, Column $column) {
                    return $value;
                })
                ->html(),

            Column::make("Start Date", 'checkout_id')
                ->format(function ($value, $row, Column $column) {
                    return $row->checkout->start_date ?? "";
                })
                ->html(),

            Column::make("End Date", 'checkout_id')
                ->format(function ($value, $row, Column $column) {
                    return $row->checkout->end_date ?? "";
                })
                ->html(),

            Column::make("Start Time", 'checkout_id')
                ->format(function ($value, $row, Column $column) {
                    return $row->checkout->start_time ?? "";
                })
                ->html(),

            Column::make("End Time", 'checkout_id')
                ->format(function ($value, $row, Column $column) {
                    return $row->checkout->end_time ?? "";
                })
                ->html(),

            Column::make("Address", 'checkout_id')
                ->format(function ($value, $row, Column $column) {
                    return $row->checkout->address->address ?? "";
                })
                ->html(),

            Column::make("State", 'checkout_id')
                ->format(function ($value, $row, Column $column) {
                    return $row->checkout->address->states->name ?? "";
                })
                ->html(),

            Column::make("City", 'checkout_id')
                ->format(function ($value, $row, Column $column) {
                    return $row->checkout->address->cities->name ?? "";
                })
                ->html(),

            Column::make("Notes", 'checkout_id')
                ->format(function ($value, $row, Column $column) {
                    return $row->checkout->note ?? "";
                })
                ->html(),

        ];
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Status')
                ->options([
                    '' => 'All',
                    'active' => 'Active',
                    'blocked' => 'Blocked',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('status', $value);
                }),

        ];
    }

    public function builder(): Builder
    {
        $modal = Booking::query();
        $modal->with("checkout", "labour");
        $modal->where("booking_status", $this->type);
        return $modal;
    }

    public function refresh(): void
    {

    }
    public function status($type)
    {
        $this->setFilter('status', $type);
    }

    public function exportSelected()
    {
        $modelData = new Booking;
        return Excel::download(new CustomExport($this->getSelected(), $modelData), 'category.xlsx');
    }
}
