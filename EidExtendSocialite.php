<?php

namespace Hofstaetter\Eid;

use SocialiteProviders\Manager\SocialiteWasCalled;

class EidExtendSocialite
{
    /**
     * Register all custom providers.
     *
     * @param \SocialiteProviders\Manager\SocialiteWasCalled $socialiteWasCalled
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite('eid', Provider::class);
    }
}

