<?php

namespace App\Http\Livewire;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Excel;
use App\Exports\CustomExport;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;
use Illuminate\Http\Request;


class UserTable extends DataTableComponent
{

    protected $model = User::class;
    public $counter = 1;
    public $type;
    public function mount(Request $request)
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
                'id' => 'user-table',
            ])
            ->setBulkActions([
                'exportSelected' => 'Export',
            ])
            ->setConfigurableAreas([
                // 'toolbar-right-end' => 'content.rapasoft.add-button',
                'toolbar-left-end' => [
                    'content.rapasoft.active-inactive',
                    [
                        'route' => 'admin.users.index',
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

            Column::make("Name", "name")
                ->format(function ($value) {
                    return $value;
                }),

            Column::make("Email", "email")
                ->format(function ($value) {
                    return $value;
                }),

            Column::make("Mobile No", "phone")
                ->format(function ($value) {
                    return $value;
                }),

            Column::make("State", "state")
                ->format(function ($value, $row, Column $column) {
                    return $row->states->name ?? "";
                })->html(),


            Column::make("City", "city")
                ->format(function ($value, $row) {
                    return $row->cities->name ?? "";
                }),


            Column::make("View", "id")
                ->format(function ($value, $row) {
                    return '<a href=' . route('admin.users.details', ["id" => $value]) . '  class="btn btn-icon btn-icon rounded-circle btn-warning waves-effect">
                    <span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z"/></svg></span>
                </a>';
                })->html(),



            // ->collap(),





            Column::make('Actions')
                ->label(function ($row, Column $column) {
                    $view_route = route('admin.users.details', $row->id);
                    $edit_route = route('admin.labours.edit', $row->id);
                    $edit_callback = 'setValue';
                    $modal = '#edit-user-modal';
                    return view('content.table-component.action', compact('edit_route',  'edit_callback', 'modal', "view_route"));
                }),
            Column::make('status')
                ->format(function ($value, $data, Column $column) {
                    $route = route('admin.labours.status');
                    return view('content.table-component.switch', compact('data', 'route'));
                }),


            Column::make('profile_pic')
                ->format(function ($row) {
                    if ($row) {
                        return '<img src="' . asset($row) . '" class="view-on-click  rounded-circle">';
                    } else {
                        return '<img src="' . asset('images/placeholder.jpg') . '" class="view-on-click  rounded-circle">';
                    }
                })
                ->html(),

            // Column::make('Created at', 'created_at')
            //     ->format(function ($value) {
            //         return '<span class="badge badge-light-success">' . date("M jS, Y h:i A", strtotime($value)) . '</span>';

            //     })
            //     ->html()
            //     ->collapseOnTablet()
            //     ->sortable(),
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
            //         $builder->where('users.name', 'like', '%' . $value . '%');
            //     }),
        ];
    }

    public function builder(): Builder
    {
        $modal = User::query()->where("type", "user");

        $modal->with("states", "cities");
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
        $modelData = new User;
        return Excel::download(new CustomExport($this->getSelected(), $modelData), 'users.xlsx');
    }
}
