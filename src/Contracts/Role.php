<?php

namespace Zhineng\Gatekeeper\Contracts;

interface Role
{
    /**
     * Retrieve the slug of the role.
     *
     * @return string
     */
    public function roleSlug(): string;
}