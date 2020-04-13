<?php
declare(strict_types=1);

namespace Inpsyde\PayPalCommerce\Button\Assets;

class DisabledSmartButton implements SmartButtonInterface
{

    public function renderWrapper(): bool
    {
        return true;
    }

    public function enqueue(): bool
    {
        return true;
    }
}