<?php
namespace App\Http\Livewire;

use App\Exports\CustomExport;
use App\Models\Areas;
use App\Models\LabourRedeem;
use Excel;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class LabourRedeemTable extends DataTableComponent
{

    protected $model = LabourRedeem::class;
    public $counter = 1;
    public function mount()
    {
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
                'id' => 'areas-table',
            ])
            ->setBulkActions([
                'exportSelected' => 'Export',
            ])
            ->setConfigurableAreas([
                // 'toolbar-right-end' => 'content.rapasoft.add-button',
                // 'toolbar-left-end' => [
                //     'content.rapasoft.active-inactive', [
                //         'route' => 'admin.areas.index',
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

            Column::make('Name', 'labour_id')
                ->format(function ($value, $row, Column $column) {
                    return $row->labour->name;
                })
                ->html(),

            Column::make('Amount', 'amount')
                ->format(function ($value, $row, Column $column) {
                    return "&#8377;" . $value;
                })
                ->html(),

            Column::make('Payment Status', 'payment_status')
                ->format(function ($value, $row, Column $column) {
                    return $value;
                })
                ->html(),

            Column::make("Accept Payment", "id")
                ->label(function ($row, Column $column) {
                    if ($row->payment_status == "pending") {
                        return "<select class='form-control' data-id=" . $row->id . " onchange='changeStatus(this)' name='payment_status' id='payment_status'>
                        <option value=''>Select</option>
                        <option value='accepted'>Accepted</option>
                        <option value='rejected'>Rejected</option>
            </select>";
                    } else {
                        return $row->payment_status;
                    }

                })->html(),

            // Column::make('Actions')
            // ->label(function ($row, Column $column) {
            //     $delete_route = route('admin.labours.destroy', $row->id);
            //     $edit_route = route('admin.redeem.accept-redeem', $row->id);
            //     $edit_callback = 'setValue';
            //     $modal = '#edit-user-modal';
            //     return view('content.table-component.action', compact('edit_route', 'edit_callback', 'modal'));
            // }),

            // Column::make('image')
            // ->format(function ($row) {
            //     if ($row) {
            //         return '<img src="' . asset($row) . '" class="view-on-click  rounded-circle">';
            //     } else {
            //         return '<img src="' . asset('images/placeholder.jpg') . '" class="view-on-click  rounded-circle">';
            //     }
            // })
            // ->html(),

            // Column::make('Location', 'created_at')
            // ->format(function ($value) {
            //     return '<span class="badge badge-light-success">' . date("M jS, Y h:i A", strtotime($value)) . '</span>';

            // })
            // ->html()
            // ->collapseOnTablet()
            // ->sortable(),
            // Column::make('Updated at', 'updated_at')
            //     ->format(function ($value) {
            //        return '<span class="badge badge-light-success">' . date("M jS, Y h:i A", strtotime($value)) . '</span>';

            //     })
            //     ->html()
            //     ->collapseOnTablet()
            //     ->sortable(),
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

            // TextFilter::make('Name')
            //     ->config([
            //         'placeholder' => 'Search Name',
            //         'maxlength' => '25',
            //     ])
            //     ->filter(function (Builder $builder, string $value) {
            //         $builder->where('areas.name', 'like', '%' . $value . '%');
            //     }),
        ];
    }

    public function builder(): Builder
    {
        $modal = LabourRedeem::query()->where("payment_status", "pending");
        $modal->with("labour");
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
        $modelData = new Areas;
        return Excel::download(new CustomExport($this->getSelected(), $modelData), 'areas.xlsx');
    }
}
