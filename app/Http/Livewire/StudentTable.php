<?php

namespace App\Http\Livewire;

use App\Exports\CustomExport;
use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Excel;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;

class StudentTable extends DataTableComponent
{

    protected $model = Student::class;
    public function mount()
    {
        $this->dispatchBrowserEvent('table-refreshed');
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            // ->setPerPageAccepted([10, 25, 50, 100, 200])
            ->setDefaultSort('id', 'desc')
            ->setEmptyMessage('No Result Found')
            ->setTableAttributes([
                'id' => 'student-table',
                // 'class' => 'this that',
            ])
            // ->setDebugEnabled()
            ->setBulkActions([
                'exportSelected' => 'Export',
            ])
            ->setConfigurableAreas([
                'toolbar-right-end' => 'content.rapasoft.add-button',
                // 'toolbar-right-start' => 'content.rapasoft.export-button',
            ]);
    }

    public function columns(): array
    {
        return [
            Column::make('Action')
                ->label(function ($row, Column $column) {
                    $delete_route = route('admin.students.index');
                    $edit_route = route('admin.students.edit', $row->id);
                    $edit_callback = 'setValue';
                    $modal = '#edit-student-modal';



                    // $modal = '#edit-appointment-modal';
                    // $delete_route = route('doctor.appointments.destroy', $row->id);
                    // $show_route = route('doctor.appointments.show', $row->id);
                    // $custom = ' <a href="' . $show_route . '"><button class="btn btn-icon btn-icon rounded-circle btn-success waves-effect">
                    // <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></button></a> ';
                    $custom = '<button title="Waiting List" data-target="#appointmentModal" id="waitingModal" data-id="' . $row->id . '" class="btn btn-icon btn-icon rounded-circle p-0 waves-effect">
                    <?xml version="1.0" encoding="UTF-8"?>
    <svg height="40" width="40" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 11.85 11.85"><defs><style>.cls-1-waiting-list{fill:#144880;}.cls-2-waiting-list{fill:#fff;}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><circle class="cls-1-waiting-list" cx="5.93" cy="5.93" r="5.93"/><path class="cls-2-waiting-list" d="M5.9,5.53a0,0,0,1,1,0,.06,0,0,0,0,1,0-.06Z"/><path class="cls-2-waiting-list" d="M5.43,8.17v.26H8.24V8.17H8.09V2.65H5.58V8.17Zm1.27-3H5.87V2.92H6.7ZM7,5.83h.83V8.07H7ZM7,2.92h.83V5.16H7ZM5.87,5.83H6.7V8.07H5.87Zm0-.29a.09.09,0,0,1,.1-.1.1.1,0,0,1,0,.2A.09.09,0,0,1,5.84,5.54Z"/><path class="cls-2-waiting-list" d="M4.48,8.64s.13.07.13.17,0,.2-.13.21h-1a1,1,0,0,1-.86-.66,4.73,4.73,0,0,1-.18-1.18c-.07-.24-.07-.37.09-.43s.25.08.26.27A7.73,7.73,0,0,0,3,8.2a.61.61,0,0,0,.58.44Z"/><circle class="cls-2-waiting-list" cx="3.71" cy="5.78" r="0.42"/><path class="cls-2-waiting-list" d="M5.09,7.93a1,1,0,0,0-.34-.06h0l-.67,0A10.89,10.89,0,0,0,4,6.67a.39.39,0,0,0-.48-.33c-.21,0-.33.19-.34.45s.08,1.33.08,1.33a.38.38,0,0,0,.22.31A1.54,1.54,0,0,0,4,8.48h.78a8.63,8.63,0,0,1,.08,1.33c0,.43.32.43.42.1A6,6,0,0,0,5.09,7.93Z"/><path class="cls-2-waiting-list" d="M3.86,2.65a.8.8,0,0,0-.8.8.79.79,0,0,0,.8.79.79.79,0,0,0,.79-.79A.79.79,0,0,0,3.86,2.65Zm.69.63h0s0,0,0,0Zm-.06-.15h0l0,0h0s0,0,0,0h0s0,0,0-.05h0a.08.08,0,0,0,0,0S4.48,3.13,4.49,3.13Zm0-.06h0s0,0,0,0h0ZM4.36,3h0l0,0h0s0,0,0,0h0l0,0h0l0,0S4.35,3,4.36,3Zm0-.05h0s0,0,0,0h0Zm-.13-.09h.1v0h0l0,0h0l-.05,0h0Zm-.05,0h0a0,0,0,0,1,0,0h0s0,0,0,0ZM4,2.74H4.1a0,0,0,0,0,0,0h0l0,0H4Zm-.06,0Zm-.18,0h.13a0,0,0,0,1,0,0H3.74S3.74,2.74,3.74,2.74Zm-.05,0h0s0,0,0,0Zm-.21.09h0a0,0,0,0,1,0,0h0S3.48,2.84,3.48,2.84ZM3.31,3Zm-.12.19h0a0,0,0,0,1,0,0h0s0,0,0,0Zm0,.16h0v0h0a.43.43,0,0,0,0,0,0,0,0,0,0,0,0h0s0,0,0,0a.43.43,0,0,0,0,0h0a.11.11,0,0,0,0,.05h0Zm0,.06h0s0,0,0,0h0Zm0,.16v0h0v0h0v0h0a.09.09,0,0,0,0,0,0,0,0,0,1,0,0h0S3.15,3.56,3.15,3.55Zm0,.07s0,0,0,0h0a0,0,0,0,1,0,0Zm.06.15h0v0a0,0,0,0,1,0,0h0s0,0,0,0,0,0,0,0h0s0,0,0,0S3.23,3.77,3.23,3.77Zm0,0h0Zm0-.74h0s0,0,0,0h0l0,0h0l0,0h0s0,0,0,0l0,.05Zm.1.87h0l0,0h0l0,0h0a.08.08,0,0,1,0,0Zm0,0h0V4h0s0,0,0,0Zm0-1.08h0l0,0h0l0,0h0l0,0h0l0,0Zm.12,1.17H3.47l0,0s0,0,0,0h0l.05,0h0l0,0v0Zm.05,0h0v0h0S3.59,4.11,3.58,4.11Zm0-1.31h0l0,0h0s0,0,0,0h.11l-.05,0Zm.15,1.35H3.63v0h.13A0,0,0,0,1,3.74,4.15Zm.06,0h0a0,0,0,0,1,0,0h0Zm.16,0H3.84a0,0,0,0,1,0,0H4S4,4.15,4,4.16Zm.06,0Zm.16,0,0,0H4.06l.05,0h0l0,0h0Zm.05,0h0V4h0Zm-.05-.5H3.81s-.05,0-.05-.06h0V3.13a.06.06,0,0,1,.05-.06.06.06,0,0,1,.06.06v.32h.31a.06.06,0,0,1,.06,0A.06.06,0,0,1,4.18,3.56Zm.18.4h-.1s0,0,0,0l0,0h0a.08.08,0,0,0,0,0h0Zm0,0h0v0Zm.07-.11h0v0h0s0,0,0,0a.05.05,0,0,0,0,0h0l0,0h0Zm0-.08h0a0,0,0,0,1,0,0h0s0,0,0,0h0s0,0,0,0h0v0Zm0-.15h0v0h0s0,0,0,0,0,0,0,0h0a.07.07,0,0,0,0,0h0Zm0-.07h0a0,0,0,0,1,0,0h0s0,0,0,0h0Zm0-.07h0a0,0,0,0,1,0,0h0V3.4h0v0s0,0,0,0h0v0h0v0h0Z"/></g></g></svg>
    </button> ';

                    // return $value->appointment_for_id;
                    // return $value->doctors;
                    // return date("Y-d-m", strtotime($value->slot_start_time));


                    $custom .= ' <button type="button" data-enquiry="' . $row->id . '" data-target="#enquiryModal"
        id="reshedule-follow-up" data-toggle="tooltip" data-placement="top" data-attendDoctor="' . $row->doctors . '" data-appointmentFor="' . $row->appointment_for_id . '"  title="Reshedule Appointment" data-slotDate="' . date("Y-m-d", strtotime($row->slot_start_time)) . '"
        class="btn btn-icon btn-icon rounded-circle  btn-flat-success waves-effect p-0">
        <span><svg height="40" width="40" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12.08 12.08"><defs><style>.cls-1-resheule{fill:#64af3b;}.cls-2-reshedule{fill:#fff;}.cls-3-reshedule{opacity:0.59;}.cls-4-reshedule{fill:#231f20;}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><circle class="cls-1-resheule" cx="6.04" cy="6.04" r="6.04"/><rect x="2.47" y="3.53" width="7.14" height="5.17" rx="0.45"/><rect class="cls-2-reshedule" x="3.26" y="4.64" width="5.56" height="3.53"/><g class="cls-3-reshedule"><rect class="cls-4-reshedule" x="3.59" y="5.22" width="0.55" height="0.55"/><rect class="cls-4-reshedule" x="4.46" y="5.22" width="0.55" height="0.55"/><rect class="cls-4-reshedule" x="5.33" y="5.22" width="0.55" height="0.55"/><rect class="cls-4-reshedule" x="6.2" y="5.22" width="0.55" height="0.55"/><rect class="cls-4-reshedule" x="7.07" y="5.22" width="0.55" height="0.55"/><rect class="cls-4-reshedule" x="7.94" y="5.22" width="0.55" height="0.55"/><rect class="cls-4-reshedule" x="3.59" y="6.13" width="0.55" height="0.55"/><rect class="cls-4-reshedule" x="4.46" y="6.13" width="0.55" height="0.55"/><rect class="cls-4-reshedule" x="5.33" y="6.13" width="0.55" height="0.55"/><rect class="cls-4-reshedule" x="6.2" y="6.13" width="0.55" height="0.55"/><rect class="cls-4-reshedule" x="7.07" y="6.13" width="0.55" height="0.55"/><rect class="cls-4-reshedule" x="7.94" y="6.13" width="0.55" height="0.55"/><rect class="cls-4-reshedule" x="3.59" y="7.04" width="0.55" height="0.55"/><rect class="cls-4-reshedule" x="4.46" y="7.04" width="0.55" height="0.55"/><rect class="cls-4-reshedule" x="5.33" y="7.04" width="0.55" height="0.55"/><rect class="cls-4-reshedule" x="6.2" y="7.04" width="0.55" height="0.55"/><rect class="cls-4-reshedule" x="7.07" y="7.04" width="0.55" height="0.55"/><rect class="cls-4-reshedule" x="7.94" y="7.04" width="0.55" height="0.55"/></g><path d="M6,6.87a.49.49,0,0,0,.35-.11,1,1,0,0,0,.16-.2c0-.08,0-.11.07-.16a.55.55,0,0,0,.12-.27.34.34,0,0,0,0-.34v0s-.16-.26-.69-.26a.73.73,0,0,0-.62.26v.06s-.07,0-.07.14a.47.47,0,0,0,.09.33c.1.14.11.16.12.21a.63.63,0,0,0,.19.24A.5.5,0,0,0,6,6.87Z"/><path d="M5.38,5.68h0a5.61,5.61,0,0,1,0-.58s0-.1.28-.2a1.94,1.94,0,0,1,.68,0A1.12,1.12,0,0,1,6.64,5c.11.09.12.1.12.19v.52h0a.81.81,0,0,0-.61-.24A.89.89,0,0,0,5.38,5.68Z"/><path d="M7.52,7.64a1,1,0,0,0-.11-.39A.67.67,0,0,0,7,6.88a3.71,3.71,0,0,0-.46-.19.64.64,0,0,1-1.08,0A3.71,3.71,0,0,0,5,6.88a.67.67,0,0,0-.37.37,1,1,0,0,0-.11.39c0,.07,0,.12.08.17a5.77,5.77,0,0,0,2.8,0C7.57,7.76,7.53,7.71,7.52,7.64Z"/><path class="cls-2-reshedule" d="M6,4.88a.25.25,0,0,0-.25.25.26.26,0,1,0,.51,0h0A.26.26,0,0,0,6,4.88Zm.17.3H6.09v.11h0V5.18H6M6,5.1H6V5H6v.15h.12Z"/><circle class="cls-2-reshedule" cx="8.48" cy="7.81" r="1.3"/><path class="cls-4-reshedule" d="M8.48,9.13a1.33,1.33,0,1,1-.23-2.65.89.89,0,0,1,.23,0,1.33,1.33,0,0,1,0,2.65Zm0-2.61A1.28,1.28,0,1,0,9.76,7.8,1.28,1.28,0,0,0,8.48,6.52Z"/><rect class="cls-4-reshedule" x="8.27" y="6.23" width="0.41" height="0.27" rx="0.11"/><path class="cls-4-reshedule" d="M9,7.8h-.5V7.29a.09.09,0,1,0-.18,0v.59h0A.09.09,0,0,0,8.45,8H9a.09.09,0,0,0,.09-.09h0A.09.09,0,0,0,9,7.8Z"/><path class="cls-4-reshedule" d="M8.13,6.68h0Zm-.32.16H7.74l.06-.06h0l.06-.06h0ZM7.62,7h0v.11h0Zm-.06.11h0l0,.06h0v0h0V7.17h0V7.1h0Zm-.13.32h0v.08h0v.06h0V7.51h0a.11.11,0,0,0,0-.08h0Zm0,.35h0v.06h0v.06h0V7.84h0v0Zm0,.35h.08V8.08h0V8Zm.15.32h.06l0,0h0l-.06-.06h0V8.39Zm.25.25h0l.08,0h0l-.07,0h0Zm.35.11Zm.53.06h0ZM9,8.8Zm.29-.16h0l-.06.06h0l-.06.05h.16Zm.21-.29h0v.07h0v.07h0ZM9.57,8h0a.11.11,0,0,1,0,.08h0v.08h0Zm0-.36h0a.11.11,0,0,0,0,.08h0v.09h0V7.77h0Zm-.09-.34h0v.07h0v.08h0V7.32ZM9.27,7h0l.07.1h0a.18.18,0,0,1,.05.07h0V7.13h0ZM9,6.78Zm-.33-.07Zm-.2,0h0Z"/><rect class="cls-2-reshedule" x="3.35" y="4.4" width="5.39" height="0.1"/><rect class="cls-4-reshedule" x="4.09" y="2.95" width="0.7" height="1.05" rx="0.34"/><path class="cls-2-reshedule" d="M4.45,4h0a.4.4,0,0,1-.4-.39V3.28a.4.4,0,0,1,.4-.39h0a.38.38,0,0,1,.39.39v.37A.38.38,0,0,1,4.45,4Zm0-1a.29.29,0,0,0-.3.28h0v.37a.29.29,0,0,0,.29.29h0a.29.29,0,0,0,.29-.29h0V3.28A.29.29,0,0,0,4.45,3Z"/><rect class="cls-4-reshedule" x="7.29" y="2.95" width="0.7" height="1.05" rx="0.34"/><path class="cls-2-reshedule" d="M7.65,4h0a.38.38,0,0,1-.39-.39V3.28a.38.38,0,0,1,.39-.39h0A.41.41,0,0,1,8,3.28v.37A.41.41,0,0,1,7.65,4Zm0-1a.29.29,0,0,0-.29.29h0v.37a.29.29,0,0,0,.29.29h0a.29.29,0,0,0,.29-.29h0V3.28A.29.29,0,0,0,7.65,3Z"/></g></g></svg></span>
    </button>';
                    $custom .= ' <button type="button" data-enquiry="' . $row->id . '" data-target="#cancelAppointmentModel"
        id="cancel-appointment-model" data-toggle="tooltip" data-placement="top" title="Cancel Appointment"
        class="btn btn-icon btn-icon rounded-circle  btn-flat-success waves-effect p-0">
        <span><svg height="40" width="40" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 11.94 11.94"><defs><style>.cls-1-cancel{opacity:0.45;}.cls-2-cancel{fill:red;}.cls-3{fill:#fff;}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><g class="cls-1-cancel"><circle class="cls-2-cancel" cx="5.97" cy="5.97" r="5.97"/></g><path d="M6,5.06,7.3,3.69a.3.3,0,0,1,.42,0s0,0,0,0l.63.61L6.84,5.9,8.57,7.6l-.67.69a.3.3,0,0,1-.42.06l-.06-.06L6,6.85,4.65,8.2a.3.3,0,0,1-.42.06L4.17,8.2l-.63-.61L5.08,6.06,3.35,4.36,4,3.67a.29.29,0,0,1,.4-.08.3.3,0,0,1,.08.08Z"/><path class="cls-3" d="M6.16,5.18,7.34,4a.27.27,0,0,1,.38,0l0,0,.55.55L7,5.93l1.54,1.5L7.91,8a.27.27,0,0,1-.38.05.12.12,0,0,1,0-.05L6.16,6.75,5,8A.26.26,0,0,1,4.61,8L4.55,8,4,7.42,5.35,6,3.82,4.5l.6-.61a.26.26,0,0,1,.36-.06l.06.06Z"/></g></g></svg></span>
    </button>';



                    // return view('content.table-component.three-dots-action', compact('custom'));
                    // return view('content.table-component.three-dots-action', compact('delete_route', 'edit_route', 'edit_callback', 'modal'));
                    return view('content.table-component.action', compact('edit_route', 'delete_route', 'edit_callback', 'modal'));
                }),
            Column::make('status')
                ->format(function ($value, $data, Column $column) {
                    $route = route('admin.students.status');
                    return view('content.table-component.switch', compact('data', 'route'));
                }),
            Column::make('SrNo.', 'id')
                ->sortable()
                ->format(function ($value, $row, Column $column) {
                    return $row->counter;
                })
                ->html(),

            // Column::make("id")
            //     ->hideIf(true)
            //     ->sortable()
            //     ->searchable(),
            Column::make('image')
                ->format(function ($row) {
                    if ($row) {
                        return '<img src="' . asset($row) . '" alt="" srcset="" class="view-on-click  rounded-circle">';
                    } else {
                        return '<img src="' . asset('images/placeholder.jpg') . '" alt="" srcset="" class="view-on-click  rounded-circle">';
                    }
                })
                ->html(),

            Column::make("Name")
                ->format(function ($value) {
                    return '<span style="display:block;width:200px !important;white-space:normal">' . $value . '</span>';
                })
                ->collapseOnTablet()
                ->sortable()
                ->searchable()
                ->html(),
            Column::make("rollno")
                ->collapseOnTablet()
                ->searchable()
                ->sortable(),
            Column::make("city")
                ->collapseOnTablet()
                ->searchable()
                ->sortable(),
            Column::make("state")
                ->collapseOnTablet()
                ->searchable()
                ->sortable(),
            Column::make("Created at", "created_at")
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
            TextFilter::make('Name')
                ->config([
                    'placeholder' => 'Search Name',
                    'maxlength' => '25',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('students.name', 'like', '%' . $value . '%');
                }),
            TextFilter::make('state')
                ->config([
                    'placeholder' => 'Search state',
                    'maxlength' => '25',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('students.state', 'like', '%' . $value . '%');
                }),
        ];
    }

    public function builder(): Builder
    {
        $modal = Student::query();

        //for serial number in proper order
        $modal->selectRaw('* ,ROW_NUMBER() OVER (ORDER BY students.id DESC) AS counter');
        return $modal;
    }

    public function refresh(): void
    {
    }

    public function exportSelected()
    {
        $modelData = new Student;
        return Excel::download(new CustomExport($this->getSelected(), $modelData), 'contactus.xlsx');
    }
}
