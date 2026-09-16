<?php
// src/Default/PressBundle/KosovoPressBundle.php
namespace DefaultTemplate\PressBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class DefaultTemplatePressBundle extends Bundle
{
    public function getParent()
    {
        return 'WebmastersAfricaPressBundle';
    }
}