<?php

namespace App\View\Components;

use Illuminate\Support\Collection;
use Illuminate\View\Component;

class ImageUploader extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $name;
    public $id;
    public $preview;
    public $onlyPreview;
    public function __construct(string $name, string $id, array|Collection $preview = [], bool $onlyPreview = false)
    {
        $this->name = $name;
        $this->id = $id;
        $this->preview = $preview;
        $this->onlyPreview = $onlyPreview;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.image-uploader');
    }
}
