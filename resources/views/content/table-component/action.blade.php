@php

    if (empty($custom)) {
        $custom = '';
    }

    $edit = '';
    if (!empty($edit_route)) {
        $data = ['edit' => $edit_route, 'modal' => $modal, 'callback' => $edit_callback ?? 'noCallback'];
        $class = 'btn-icon btn-icon rounded-circle btn-warning';
        $icon = '<svg aria-hidden="true" focusable="false" data-prefix="fa-duotone" data-icon="pen-nib" class="svg-inline--fa fa-pen-nib fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
    <defs>
        <style>
            .fa-secondary {
                opacity: .4
            }

        </style>
    </defs>
    <g class="fa-group">
        <path class="fa-primary" d="M497.9 74.19l-60.13-60.13c-18.75-18.75-49.24-18.74-67.98 .0065l-81.87 81.98l127.1 127.1l81.98-81.87C516.7 123.4 516.7 92.94 497.9 74.19z" fill="currentColor" />
        <path class="fa-secondary" d="M136.6 138.8c-20.37 5.749-36.62 21.25-43.37 41.37L0 460l14.75 14.75l149.1-150.1c-2.1-6.249-4.749-13.25-4.749-20.62c0-26.5 21.5-47.99 47.99-47.99s47.99 21.5 47.99 47.99s-21.5 47.99-47.99 47.99c-7.374 0-14.37-1.75-20.62-4.749l-150.1 149.1L51.99 512l279.8-93.24c20.12-6.749 35.62-22.1 41.37-43.37l42.75-151.4l-127.1-127.1L136.6 138.8z" fill="currentColor" />
    </g>
</svg>';
        $edit = view('content.table-component.button', compact('data', 'icon', 'class'))->render();
    }

    $delete = '';
    if (!empty($delete_route)) {
        $data = ['delete' => $delete_route];
        $class = 'btn-icon btn-icon rounded-circle btn-danger';
        $icon = '<svg aria-hidden="true" focusable="false" data-prefix="fa-duotone" data-icon="trash-can" class="svg-inline--fa fa-trash-can fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
    <defs>
        <style>
            .fa-secondary {
                opacity: .4
            }

        </style>
    </defs>
    <g class="fa-group">
        <path class="fa-primary" d="M432 32H320l-11.58-23.16c-2.709-5.42-8.25-8.844-14.31-8.844H153.9c-6.061 0-11.6 3.424-14.31 8.844L128 32H16c-8.836 0-16 7.162-16 16V80c0 8.836 7.164 16 16 16h416c8.838 0 16-7.164 16-16V48C448 39.16 440.8 32 432 32z" fill="currentColor" />
        <path class="fa-secondary" d="M32 96v368C32 490.5 53.5 512 80 512h288c26.5 0 48-21.5 48-48V96H32zM144 416c0 8.875-7.125 16-16 16S112 424.9 112 416V192c0-8.875 7.125-16 16-16S144 183.1 144 192V416zM240 416c0 8.875-7.125 16-16 16S208 424.9 208 416V192c0-8.875 7.125-16 16-16s16 7.125 16 16V416zM336 416c0 8.875-7.125 16-16 16s-16-7.125-16-16V192c0-8.875 7.125-16 16-16s16 7.125 16 16V416z" fill="currentColor" />
    </g>
</svg>';
        $delete = view('content.table-component.button', compact('data', 'icon', 'class'))->render();
    }

    $restore = '';
    if (!empty($restore_route)) {
        $data = ['restore' => $restore_route];
        $class = 'btn btn-icon btn-icon rounded-circle btn-warning waves-effect restore-btn';
        $icon = '<svg xmlns="http://www.w3.org/2000/svg" title="restore" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-ccw">
    <polyline points="1 4 1 10 7 10"></polyline>
    <polyline points="23 20 23 14 17 14"></polyline>
    <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"></path>
</svg>';
        $delete = view('content.table-component.button', compact('data', 'icon', 'class'))->render();
    }

    $view_route = '';
    if (!empty($view_route)) {
        $data = ['view_route' => $view_route];
        $class = 'btn btn-icon btn-icon rounded-circle btn-warning waves-effect restore-btn text-white';
        $icon =
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z"/></svg>';
        $view_route = view('content.table-component.button', compact('data', 'icon', 'class'))->render();
    }
@endphp

{!!  $view_route . " " . $edit . ' ' . $delete . ' ' . $restore . ' ' . $custom  !!}
