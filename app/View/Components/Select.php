<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Select extends Component
{
    public $name;
    public $options;
    public $multiple;
    public $class;
    public $attrs;
    public $required;
    public $additionalOptionText;
    public $label;
    public $optionValue;
    public $array;
    public $id;
    public function __construct(
        string $name,
        array|object $options = [],
        bool $multiple = false,
        string $class = "",
        $attrs = "",
        bool $required = true,
        array $additionalOptionText = [],
        string|bool $label = false,
        string $optionValue = null,
        bool $array = false,
        $id = null
    ) {
        $this->name = $name;
        $this->options = $options;
        $this->multiple = $multiple;
        $this->class = $class;
        $this->attrs = $attrs;
        $this->required = $required;
        $this->additionalOptionText = $additionalOptionText;
        $this->label = $label;
        $this->optionValue = $optionValue;
        $this->array = $array;
        $this->id = $id;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.select');
    }
}
