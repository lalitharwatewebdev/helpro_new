<?php
namespace App\Http\Livewire;

use App\Exports\CustomExport;
use App\Models\User;
use Excel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class UserTable extends DataTableComponent
{

    protected $model = User::class;
    public $counter  = 1;
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

            Column::make("Name", "name")
                ->format(function ($value) {
                    return $value;
                })->searchable(),

            Column::make("Email", "email")
                ->format(function ($value) {
                    return $value;
                })->searchable(),

            Column::make("Mobile No", "phone")
                ->format(function ($value) {
                    return $value;
                })->searchable(),

            Column::make("State", "state")
                ->format(function ($value, $row, Column $column) {
                    return $row->states->name ?? "";
                })->html()->searchable(),

            Column::make("City", "city")
                ->format(function ($value, $row) {
                    return $row->cities->name ?? "";
                })->searchable(),

            Column::make("View", "id")
                ->format(function ($value, $row) {
                    return '<a href=' . route('admin.users.details', ["id" => $value]) . '  class="btn btn-icon btn-icon rounded-circle btn-warning waves-effect">
                    <span><i class="fa-solid fa-eye"></i></span>
                </a>';
                })->html(),

            // ->collap(),

            Column::make('Actions')
                ->label(function ($row, Column $column) {
                    $view_route    = route('admin.users.details', $row->id);
                    $edit_route    = route('admin.labours.edit', $row->id);
                    $edit_callback = 'setValue';
                    $modal         = '#edit-user-modal';
                    return view('content.table-component.action', compact('edit_route', 'edit_callback', 'modal', "view_route"));
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
                    ''        => 'All',
                    'active'  => 'Active',
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
