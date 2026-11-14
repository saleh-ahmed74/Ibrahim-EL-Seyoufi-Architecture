<?php

namespace App\Livewire\Admin;

use App\Models\Inquiry;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class Inquiries extends AdminComponent
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        Inquiry::query()->findOrFail($id)->delete();
        session()->flash('status', 'Inquiry deleted.');
    }

    public function render()
    {
        return view('livewire.admin.inquiries', [
            'inquiries' => Inquiry::query()
                ->when($this->search, fn ($query) => $query->where(fn ($query) => $query
                    ->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%')
                    ->orWhere('message', 'like', '%'.$this->search.'%')))
                ->latest()
                ->paginate(15),
        ]);
    }
}
