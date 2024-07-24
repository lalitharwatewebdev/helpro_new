<?php

namespace App\Http\Livewire;

use App\Exports\CustomExport;
use App\Models\Student;
use App\Models\Subject;
use Excel;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;

class SubjectTable extends DataTableComponent
{

    protected $model = Subject::class;
    public function mount()
    {
        $this->dispatchBrowserEvent('table-refreshed');
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setFilterPillsStatus(false);
        $this->setPrimaryKey('id')
            // ->setDebugEnabled()

            ->setDefaultSort('id', 'desc')
            ->setEmptyMessage('No Result Found')
            ->setTableAttributes([
                'id' => 'subject-table',
            ])
            ->setBulkActions([
                'exportSelected' => 'Export',
            ])
            ->setConfigurableAreas([
                'toolbar-right-end' => 'content.rapasoft.add-button',
                // 'toolbar-right-start' => 'content.rapasoft.export-button',
                'toolbar-left-end' => [
                    'content.rapasoft.active-inactive', [
                        'route' => 'admin.subjects.index',
                    ]
                ]
            ]);
    }

    public function columns(): array
    {
        return [
            Column::make('||')
                ->label(function ($row, Column $column) {
                    $delete_route = route('admin.subjects.destroy', $row->id);
                    $edit_route = route('admin.subjects.edit', $row->id);
                    $edit_callback = 'setValue';
                    $modal = '#edit-subject-modal';
                    return view('content.table-component.three-dots-action', compact('delete_route', 'edit_route', 'edit_callback', 'modal'));
                }),
            Column::make('status')
                ->format(function ($value, $data, Column $column) {
                    $route = route('admin.subjects.status');
                    return view('content.table-component.switch', compact('data', 'route'));
                }),
            Column::make('SrNo.', 'id')
                ->sortable()
                ->searchable()
                ->format(function ($value, $row, Column $column) {
                    return $row->counter;
                })
                ->html(),
            Column::make('name')
                ->sortable()
                ->searchable(),
            Column::make('student', 'student_id')
                ->format(function ($value, $row, Column $column) {
                    return '<span style="display:block;width:200px !important;white-space:normal">' . $row->student->name ?? "" . '</span>';
                })
                ->html()
                ->sortable()
                ->searchable(),

            Column::make('Created at', 'created_at')
                ->format(function ($value) {
                    return '<span class="badge badge-light-success">' . $value . '</span>';
                })
                ->html()
                ->collapseOnTablet()
                ->sortable(),
            Column::make('Updated at', 'updated_at')
                ->format(function ($value) {
                    return '<span class="badge badge-light-success">' . $value . '</span>';
                })
                ->html()
                ->collapseOnTablet()
                ->sortable(),
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
                ->config([
                    'class' => 'select2',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('status', $value);
                }),

            TextFilter::make('Name')
                ->config([
                    'placeholder' => 'Search Name',
                    'maxlength' => '25',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('subjects.name', 'like', '%' . $value . '%');
                }),
            SelectFilter::make('student')
                ->options(
                    Student::query()
                        ->orderBy('name')
                        ->get()
                        ->keyBy('id')
                        ->map(fn ($tag) => $tag->name)
                        ->toArray()
                )
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('subjects.student_id', $value);
                }),
        ];
    }

    public function builder(): Builder
    {
        $modal = Subject::query();

        //for serial number in proper order
        $modal->selectRaw('* ,ROW_NUMBER() OVER (ORDER BY subjects.id DESC) AS counter');
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
        $modelData = new Subject;
        return Excel::download(new CustomExport($this->getSelected(), $modelData), 'contactus.xlsx');
    }
}
