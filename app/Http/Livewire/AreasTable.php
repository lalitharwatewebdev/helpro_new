<?php
namespace App\Http\Livewire;

use App\Models\Areas;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Excel;
use App\Exports\CustomExport;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;

        
class AreasTable extends DataTableComponent
{

    protected $model = Areas::class;
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
                'toolbar-right-end' => 'content.rapasoft.add-button',
                'toolbar-left-end' => [
                    'content.rapasoft.active-inactive', [
                        'route' => 'admin.areas.index',
                    ]
                ]
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

            Column::make('Latitude', 'latitude')
            ->format(function ($value, $row, Column $column) {
                return $value;
            })
            ->html(),

            Column::make('Longtitude', 'longitude')
            ->format(function ($value, $row, Column $column) {
                return $value;
            })
            ->html(),

            Column::make('Radius', 'radius')
            ->format(function ($value, $row, Column $column) {
                return $value;
            })
            ->html(),

            Column::make('Price', 'price')
            ->format(function ($value, $row, Column $column) {
                return $value;
            })
            ->html(),

            Column::make('Category', 'category_id')
            ->format(function ($value, $row, Column $column) {
                return $row->category->title;
            })
            ->html(),

            Column::make('Actions')
                ->label(function ($row, Column $column) {
                    $delete_route = route('admin.areas.destroy', $row->id);
                    $edit_route = route('admin.areas.edit', $row->id);
                    $edit_callback = 'setValue';
                    $modal = '#edit-areas-modal';
                    return view('content.table-component.action', compact('edit_route', 'delete_route', 'edit_callback', 'modal'));
                }),
            Column::make('status')
                ->format(function ($value, $data, Column $column) {
                    $route = route('admin.areas.status');
                    return view('content.table-component.switch', compact('data', 'route'));
                }),
                 
                
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
        $modal = Areas::query();
        $modal->with("category");
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
        