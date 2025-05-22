@vite('resources/css/app.css')

<div>
    {{ ($this->delete)(['product' => $product->id]) }}

    <x-filament-actions::modals />
</div>
