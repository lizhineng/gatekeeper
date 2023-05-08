<?php

namespace Zhineng\Gatekeeper\Contracts;

interface HasPermissions
{
    /**
     * Determine if the entity has the given role.
     *
     * @param  Permission|iterable|string  $permission
     * @return bool
     */
    public function allows(Permission|iterable|string $permission): bool;

    /**
     * Determine if the entity has any of the given roles.
     *
     * @param  iterable  $permissions
     * @return bool
     */
    public function allowsAny(iterable $permissions): bool;

    /**
     * Assign the role to the entity.
     *
     * @param  Permission|iterable|string  $permission
     * @return self
     */
    public function assignPermission(Permission|iterable|string $permission): self;

    /**
     * Remove the role from the entity.
     *
     * @param  Permission|iterable|string  $permission
     * @return self
     */
    public function removePermission(Permission|iterable|string $permission): self;

    /**
     * Retrieve all permissions of the entity.
     *
     * @return iterable
     */
    public function getPermissions(): iterable;
}