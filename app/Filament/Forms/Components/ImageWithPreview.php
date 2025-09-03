<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Field;
use Closure;
class ImageWithPreview extends Field
{
    protected string $view = 'filament.forms.components.image-with-preview';
    
   
    protected string|Closure|null $imageUrl = null;
    protected string|Closure|null $altText = null;
    protected int|Closure $imageHeight = 100;

    public function imageUrl(string|Closure|null $url): static
    {
        $this->imageUrl = $url;

        return $this;
    }

    
    public function altText(string|Closure|null $text): static
    {
        $this->altText = $text;

        return $this;
    }

    public function imageHeight(int|Closure $height): static
    {
        $this->imageHeight = $height;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->evaluate($this->imageUrl);
    }

    public function getAltText(): ?string
    {
        return $this->evaluate($this->altText) ?? 'Image Preview';
    }

    public function getImageHeight(): int
    {
        return $this->evaluate($this->imageHeight);
    }
}
