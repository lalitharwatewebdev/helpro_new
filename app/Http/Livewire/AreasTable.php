<?php
namespace App\Http\Livewire;

use App\Exports\CustomExport;
use App\Models\Areas;
use Excel;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

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

            Column::make('Area Name', 'area_name')
                ->format(function ($value, $row, Column $column) {
                    return $value;
                })
                ->html(),

            Column::make('Category', 'category_id')
                ->format(function ($value, $row, Column $column) {
                    return $row->category->title ?? "";
                })
                ->html(),

            Column::make('Actions')
                ->label(function ($row, Column $column) {
                    // $delete_route = route('admin.areas.destroy', $row->id);
                    // $edit_route = route('admin.areas.edit',$row->id);
                    $edit_route = route('admin.areas.edit', $row->id);
                    $customEditButton = ' <a href="' . $edit_route . '"><button class="btn btn-icon btn-icon rounded-circle btn-warning waves-effect"><svg aria-hidden="true" focusable="false" data-prefix="fa-duotone" data-icon="pen-nib" class="svg-inline--fa fa-pen-nib fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><defs><style>.fa-secondary{opacity:.4}</style></defs><g class="fa-group"><path class="fa-primary" d="M497.9 74.19l-60.13-60.13c-18.75-18.75-49.24-18.74-67.98 .0065l-81.87 81.98l127.1 127.1l81.98-81.87C516.7 123.4 516.7 92.94 497.9 74.19z" fill="currentColor"/><path class="fa-secondary" d="M136.6 138.8c-20.37 5.749-36.62 21.25-43.37 41.37L0 460l14.75 14.75l149.1-150.1c-2.1-6.249-4.749-13.25-4.749-20.62c0-26.5 21.5-47.99 47.99-47.99s47.99 21.5 47.99 47.99s-21.5 47.99-47.99 47.99c-7.374 0-14.37-1.75-20.62-4.749l-150.1 149.1L51.99 512l279.8-93.24c20.12-6.749 35.62-22.1 41.37-43.37l42.75-151.4l-127.1-127.1L136.6 138.8z" fill="currentColor"/></g></svg></button></a> ';

                    $delete_route = route('admin.areas.destroy', $row->id);
                    $customEditButton .= '<button type="button" data-delete="' . $delete_route . '" class="btn btn-icon btn-icon rounded-circle btn-danger waves-effect">
                <span> <svg aria-hidden="true" focusable="false" data-prefix="fa-duotone" data-icon="trash-can" class="svg-inline--fa fa-trash-can fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><defs><style>.fa-secondary{opacity:.4}</style></defs><g class="fa-group"><path class="fa-primary" d="M432 32H320l-11.58-23.16c-2.709-5.42-8.25-8.844-14.31-8.844H153.9c-6.061 0-11.6 3.424-14.31 8.844L128 32H16c-8.836 0-16 7.162-16 16V80c0 8.836 7.164 16 16 16h416c8.838 0 16-7.164 16-16V48C448 39.16 440.8 32 432 32z" fill="currentColor"></path><path class="fa-secondary" d="M32 96v368C32 490.5 53.5 512 80 512h288c26.5 0 48-21.5 48-48V96H32zM144 416c0 8.875-7.125 16-16 16S112 424.9 112 416V192c0-8.875 7.125-16 16-16S144 183.1 144 192V416zM240 416c0 8.875-7.125 16-16 16S208 424.9 208 416V192c0-8.875 7.125-16 16-16s16 7.125 16 16V416zM336 416c0 8.875-7.125 16-16 16s-16-7.125-16-16V192c0-8.875 7.125-16 16-16s16 7.125 16 16V416z" fill="currentColor"></path></g></svg></span>
            </button>';
                    // return view('content.table-component.action', compact('edit_route','delete_route', 'modal', 'customEditButton'));
                    return $customEditButton;
                })->html(),
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
