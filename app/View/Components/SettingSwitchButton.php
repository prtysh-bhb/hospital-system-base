<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SettingSwitchButton extends Component
{
    public string $settingKey;

    public string $label;

    public string $description;

    public string $categoryId;

    public bool $checked;

    public string $enabledText;

    public string $disabledText;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $settingKey,
        string $label,
        string $description,
        string $categoryId,
        bool $checked = false,
        string $enabledText = 'Enabled',
        string $disabledText = 'Disabled'
    ) {
        $this->settingKey = $settingKey;
        $this->label = $label;
        $this->description = $description;
        $this->categoryId = $categoryId;
        $this->checked = $checked;
        $this->enabledText = $enabledText;
        $this->disabledText = $disabledText;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('admin.settings.components.setting-switch-button');
    }
}
