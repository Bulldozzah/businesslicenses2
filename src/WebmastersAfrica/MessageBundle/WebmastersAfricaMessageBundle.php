<?php

namespace WebmastersAfrica\MessageBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class WebmastersAfricaMessageBundle extends Bundle
{
	public function getParent()
    {
        return "FOSMessageBundle";
    }
}
