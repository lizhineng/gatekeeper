<?php

namespace Zhineng\Gatekeeper\Contracts;

interface Permission
{
    /**
     * Retrieve the slug of the permission.
     *
     * @return string
     */
    public function permissionSlug(): string;
}