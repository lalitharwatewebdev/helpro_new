<?php
namespace App\Http\Livewire;

use App\Exports\CustomExport;
use App\Models\Category;
use Excel;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class CategoryTable extends DataTableComponent
{

    protected $model = Category::class;
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
                'id' => 'category-table',
            ])
            ->setBulkActions([
                'exportSelected' => 'Export',
            ])
            ->setConfigurableAreas([
                'toolbar-right-end' => 'content.rapasoft.add-button',
                'toolbar-left-end' => [
                    'content.rapasoft.active-inactive', [
                        'route' => 'admin.category.index',
                    ],
                ],
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

            Column::make("Name", 'title')
                ->format(function ($value, $row, Column $column) {
                    return $value;
                })
                ->html(),
            Column::make("percentage_for_less_than")
                ->collapseOnTablet()
                ->searchable()
                ->sortable(),
            Column::make("percentage_for_more_than")
                ->collapseOnTablet()
                ->searchable()
                ->sortable(),

            Column::make('image')
                ->format(function ($row) {
                    if ($row) {
                        return '<img src="' . asset($row) . '" class="view-on-click  rounded-circle">';
                    } else {
                        return '<img src="' . asset('images/placeholder.jpg') . '" class="view-on-click  rounded-circle">';
                    }
                })
                ->html(),

            // Column::make('Created at', 'created_at')
            // ->format(function ($value) {
            //     return '<span class="badge badge-light-success">' . date("M jS, Y h:i A", strtotime($value)) . '</span>';

            // })
            // ->html()
            // ->collapseOnTablet()
            // ->sortable(),
            Column::make('Actions')
                ->label(function ($row, Column $column) {
                    $delete_route = route('admin.category.destroy', $row->id);
                    $edit_route = route('admin.category.edit', $row->id);
                    $edit_callback = 'setValue';
                    $modal = '#edit-category-modal';
                    return view('content.table-component.action', compact('edit_route', 'delete_route', 'edit_callback', 'modal'));
                }),
            Column::make('status')
                ->format(function ($value, $data, Column $column) {
                    $route = route('admin.category.status');
                    return view('content.table-component.switch', compact('data', 'route'));
                }),
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
            //         $builder->where('categorys.name', 'like', '%' . $value . '%');
            //     }),
        ];
    }

    public function builder(): Builder
    {
        $modal = Category::query();
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
        $modelData = new Category;
        return Excel::download(new CustomExport($this->getSelected(), $modelData), 'category.xlsx');
    }
}
