<?php

namespace Zhineng\Gatekeeper\Tests;

use Zhineng\Gatekeeper\Exceptions\CouldNotFindPermission;
use Zhineng\Gatekeeper\Exceptions\CouldNotFindRole;
use Zhineng\Gatekeeper\Models\Permission;
use Zhineng\Gatekeeper\Models\Role;

class HasRolesTest extends FeatureTest
{
    /**
     * @dataProvider provides_roles
     */
    public function test_assigns_role_to_user($getRoles)
    {
        $roles = $getRoles();

        $user = $this->makeUser();
        $this->assertFalse($user->hasRole($roles));

        $user->assignRole($roles);
        $this->assertTrue($user->fresh()->hasRole($roles));
    }

    public function test_assigns_not_exists_role_to_user()
    {
        $this->expectException(CouldNotFindRole::class);
        $this->expectExceptionMessage("Could not retrieve the role by given name [foo].");
        $this->makeUser()->assignRole('foo');
    }

    /**
     * @dataProvider provides_roles
     */
    public function test_removes_role_from_user($getRoles)
    {
        $roles = $getRoles();

        $user = $this->makeUser();

        $user->assignRole($roles);
        $this->assertTrue($user->hasRole($roles));

        $user->removeRole($roles);
        $this->assertFalse($user->fresh()->hasRole($roles));
    }

    public function test_checks_user_permission()
    {
        $readPosts = Permission::create(['name' => 'read:posts']);
        $editor = Role::create(['name' => 'editor'])->assignPermission($readPosts);
        $user = $this->makeUser()->assignRole($editor);
        $this->assertTrue($user->allows($readPosts));
    }

    public function test_checks_user_permission_by_scope()
    {
        $readPosts = Permission::create(['name' => 'read:posts']);
        $editor = Role::create(['name' => 'editor'])->assignPermission($readPosts);
        $user = $this->makeUser()->assignRole($editor);
        $this->assertTrue($user->allows($readPosts->name));
    }

    public function test_expects_exception_when_checking_with_not_exists_permission_scope()
    {
        $this->expectException(CouldNotFindPermission::class);
        $this->expectExceptionMessage("Could not retrieve the permission by given name [foo].");
        $this->makeUser()->allows('foo');
    }

    public function test_checks_user_has_any_permissions()
    {
        $readPosts = Permission::create(['name' => 'read:posts']);
        $writePosts = Permission::create(['name' => 'write:posts']);
        $editor = Role::create(['name' => 'editor'])->assignPermission($readPosts);
        $user = $this->makeUser()->assignRole($editor);
        $this->assertTrue($user->allowsAny([$readPosts, $writePosts]));
    }

    public function provides_roles()
    {
        return [
            'role model' => [
                fn () => Role::create(['name' => 'editor']),
            ],
            'role name' => [
                fn () => Role::create(['name' => 'editor'])->name,
            ],
            'multiple role models' => [
                fn () => [
                    Role::create(['name' => 'admin']),
                    Role::create(['name' => 'editor']),
                ],
            ],
            'multiple role names' => [
                fn () => [
                    Role::create(['name' => 'admin'])->name,
                    Role::create(['name' => 'editor'])->name,
                ],
            ],
            'multiple roles' => [
                fn () => [
                    Role::create(['name' => 'admin']),
                    Role::create(['name' => 'editor'])->name,
                ],
            ],
        ];
    }
}