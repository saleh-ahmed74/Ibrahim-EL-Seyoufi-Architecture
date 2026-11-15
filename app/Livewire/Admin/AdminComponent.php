<?php

namespace App\Livewire\Admin;

use Livewire\Component;

abstract class AdminComponent extends Component
{
    public function boot(): void
    {
        abort_unless(auth()->user()?->is_admin, 403);
    }

    protected function notifyAdmin(string $message, string $type = 'success'): void
    {
        session()->flash('status', $message);
        $this->dispatch('admin-action-feedback', message: $message, type: $type);
    }
}
