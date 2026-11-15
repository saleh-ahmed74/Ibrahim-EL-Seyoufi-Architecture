<?php

namespace App\Livewire\Site;

use App\Support\HomepageCopy;
use App\Support\PublicContent;
use Livewire\Attributes\Locked;
use Livewire\Component;

class CategoryMenu extends Component
{
    public array $menuCopy = [];

    #[Locked]
    public bool $loaded = false;

    #[Locked]
    public array $categories = [];

    public function loadCategories(PublicContent $content): void
    {
        if ($this->loaded) {
            return;
        }

        $this->categories = $content->categoryMenu()
            ->map(fn ($category): array => ['name' => $category->name, 'slug' => $category->slug])
            ->all();
        $this->loaded = true;
    }

    public function render()
    {
        return view('livewire.site.category-menu', [
            'copy' => array_replace_recursive(HomepageCopy::defaults(), $this->menuCopy),
        ]);
    }
}
