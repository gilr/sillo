<?php

use App\Models\{ Submenu, Page, Post, Serie, Category };
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Mary\Traits\Toast;
use Illuminate\Validation\Rule;

new 
#[Layout('components.layouts.admin')]
class extends Component {

    use Toast;

    public Submenu $submenu;
    public string $label = '';
    public string $link = '';

    public int $subPost = 0;
    public int $subPage = 0;
    public int $subSerie = 0;
    public int $subCategory = 0;
    public int $subOption = 1;

    // Initialise le composant avec le sous-menu donné.
    public function mount(Submenu $submenu): void
    {
        $this->submenu = $submenu;
        $this->fill($this->submenu);
    }

    // Met à jour le libellé et le lien en fonction de la sous-option sélectionnée.
    public function updating($property, $value): void
    {
        if ($value != '') {
            switch ($property) {
                case 'subPost':
                    $post = Post::find($value);
                    if ($post) {
                        $this->label = $post->title;
                        $this->link = route('posts.show', $post->slug);
                    }
                    break;
                case 'subPage':
                    $page = Page::find($value);
                    if ($page) {
                        $this->label = $page->title;
                        $this->link = route('pages.show', $page->slug);
                    }
                    break;
                case 'subSerie':
                    $serie = Serie::find($value);
                    if ($serie) {
                        $this->label = $serie->title;
                        $this->link = url('serie/' . $serie->slug);
                    }
                    break;
                case 'subCategory':
                    $category = Category::find($value);
                    if ($category) {
                        $this->label = $category->title;
                        $this->link = url('category/' . $category->slug);
                    }
                    break;
                case 'subOption':
                    $this->label = '';
                    $this->link = '';
                    $this->subPost = 0;
                    $this->subPage = 0;
                    $this->subSerie = 0;
                    $this->subCategory = 0;
                    break;
            }
        }
    }

    // Enregistrer les modifications apportées au sous-menu.
    public function save(): void
    {
        $data = $this->validate([
            'label' => [
                'required',
                'string',
                'max:255',
                Rule::unique('menus')->ignore($this->submenu->id),
            ],
            'link' => 'required|url',
        ]);

        $this->submenu  ->update($data);

        $this->success(__('Menu updated with success.'), redirectTo: '/admin/menus/index');
    }

    // Renvoie les données nécessaires pour le rendu du composant.
    public function with(): array
    {
        return [
            'pages' => Page::select('id', 'title', 'slug')->get(),
            'posts' => Post::select('id', 'title', 'slug', 'created_at')->latest()->take(10)->get(),
            'series' => Serie::select('id', 'title', 'slug')->get(),
            'categories' => Category::all(),
            'subOptions' => [
                ['id' => 1, 'name' => __('Post'),], 
                ['id' => 2, 'name' => __('Page'),],
                ['id' => 3, 'name' => __('Serie'),],
                ['id' => 4, 'name' => __('Category'),],
            ],
        ];
    }

}; ?>

<div>
    <x-card class="" title="{{__('Edit a submenu')}}">

        <x-form wire:submit="save()">
            <x-radio :options="$subOptions" wire:model="subOption"  wire:change="$refresh" />
            @if($subOption == 1)
                <x-select 
                    label="{{__('Post')}}" 
                    option-label="title" 
                    :options="$posts"
                    placeholder="{{__('Select a post')}}"
                    wire:model="subPost"
                    wire:change="$refresh" />
            @elseif($subOption == 2)
                <x-select 
                    label="{{__('Page')}}" 
                    option-label="title" 
                    :options="$pages" 
                    placeholder="{{__('Select a page')}}"
                    wire:model="subPage"
                    wire:change="$refresh" />
            @elseif($subOption == 3)
                <x-select 
                    label="{{__('Serie')}}" 
                    option-label="title" 
                    :options="$series" 
                    placeholder="{{__('Select a serie')}}"
                    wire:model="subSerie"
                    wire:change="$refresh" />
            @elseif($subOption == 4)
                <x-select 
                    label="{{__('Category')}}" 
                    option-label="title" 
                    :options="$categories" 
                    placeholder="{{__('Select a category')}}"
                    wire:model="subCategory"
                    wire:change="$refresh" />
            @endif
            <x-input label="{{__('Title')}}" wire:model="label" />
            <x-input type="text" wire:model="link" label="{{ __('Link') }}" />
            <x-slot:actions>
                <x-button label="{{__('Save')}}" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
            </x-slot:actions>
        </x-form>

    </x-card>
</div>