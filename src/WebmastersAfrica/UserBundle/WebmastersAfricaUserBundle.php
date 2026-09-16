<?php

namespace WebmastersAfrica\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class WebmastersAfricaUserBundle extends Bundle
{
	public function getParent()
    {
        return "FOSUserBundle";
    }
}
