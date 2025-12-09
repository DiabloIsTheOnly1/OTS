<?php

namespace App\View\Components;

use Illuminate\View\Component;

class PartialApprove extends Component
{
    public $id;
    public $actualHm;
    public $actualMinutes;
    public $canApprove;
    public $requestedHm;
    public $requestedMinutes;

    public function __construct($id, $actualHm, $actualMinutes, $requestedHm, $requestedMinutes, $canApprove = true)
    {
        $this->id = $id;
        $this->actualHm = $actualHm;
        $this->actualMinutes = $actualMinutes;
        $this->requestedHm = $requestedHm;
        $this->requestedMinutes = $requestedMinutes;
        $this->canApprove = $canApprove;
    }

    public function render()
    {
        return view('components.partial-approve');
    }
}
