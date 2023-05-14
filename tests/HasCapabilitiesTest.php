<?php

namespace Zhineng\Gatekeeper\Tests;

use Zhineng\Gatekeeper\Exceptions\CouldNotFindPermission;
use Zhineng\Gatekeeper\Exceptions\CouldNotFindRole;
use Zhineng\Gatekeeper\Models\Permission;
use Zhineng\Gatekeeper\Models\Role;

class HasCapabilitiesTest extends FeatureTest
{
    use ProvidesRoles, ProvidesPermissions;

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

    public function test_removes_not_exists_role_from_user()
    {
        $this->expectException(CouldNotFindRole::class);
        $this->expectExceptionMessage("Could not retrieve the role by given name [foo].");
        $this->makeUser()->removeRole('foo');
    }

    public function test_has_all_roles_checking()
    {
        $user = $this->makeUser();
        $admin = Role::create(['name' => 'admin']);
        $editor = Role::create(['name' => 'editor']);
        $this->assertFalse($user->hasAllRoles([$admin, $editor]));
        $user->assignRole($admin);
        $this->assertFalse($user->fresh()->hasAllRoles([$admin, $editor]));
        $user->assignRole($editor);
        $this->assertTrue($user->fresh()->hasAllRoles([$admin, $editor]));
    }

    public function test_has_any_roles_checking()
    {
        $user = $this->makeUser();
        $admin = Role::create(['name' => 'admin']);
        $editor = Role::create(['name' => 'editor']);
        $this->assertFalse($user->hasAnyRoles([$admin, $editor]));
        $user->assignRole($admin);
        $this->assertTrue($user->fresh()->hasAnyRoles([$admin, $editor]));
    }

    public function test_checks_against_not_exists_role()
    {
        $user = $this->makeUser();
        $this->assertFalse($user->hasRole('foo'));
        $this->assertFalse($user->hasAllRoles(['foo']));
        $this->assertFalse($user->hasAnyRoles(['foo']));
    }

    /**
     * @dataProvider provides_permissions
     */
    public function test_assigns_permission_to_user($getPermissions)
    {
        $permissions = $getPermissions();
        $user = $this->makeUser();
        $this->assertFalse($user->allows($permissions));
        $user->assignPermission($permissions);
        $this->assertTrue($user->fresh()->allows($permissions));
    }

    /**
     * @dataProvider provides_permissions
     */
    public function test_removes_permission_from_user($getPermissions)
    {
        $permissions = $getPermissions();
        $user = $this->makeUser();
        $user->assignPermission($permissions);
        $this->assertTrue($user->allows($permissions));
        $user->removePermission($permissions);
        $this->assertFalse($user->fresh()->allows($permissions));
    }

    public function test_permission_could_be_obtained_from_direct_assignment()
    {
        $readPosts = Permission::create(['name' => 'read:posts']);
        $user = $this->makeUser();
        $this->assertFalse($user->allows($readPosts));
        $this->assertFalse($user->allowsThroughDirectPermission($readPosts));
        $user->assignPermission($readPosts);
        $user->refresh();
        $this->assertTrue($user->allows($readPosts));
        $this->assertTrue($user->allowsThroughDirectPermission($readPosts));
    }

    public function test_permission_could_be_obtained_from_role()
    {
        $readPosts = Permission::create(['name' => 'read:posts']);
        $editor = Role::create(['name' => 'editor'])->assignPermission($readPosts);
        $user = $this->makeUser();
        $this->assertFalse($user->allows($readPosts));
        $this->assertFalse($user->allowsThroughRole($readPosts));
        $user->assignRole($editor);
        $user->refresh();
        $this->assertTrue($user->allows($readPosts));
        $this->assertTrue($user->allowsThroughRole($readPosts));
    }

    public function test_allows_all_permissions_checking()
    {
        $readPosts = Permission::create(['name' => 'read:posts']);
        $writePosts = Permission::create(['name' => 'write:posts']);
        $editor = Role::create(['name' => 'editor'])->assignPermission($readPosts);
        $user = $this->makeUser()->assignRole($editor);
        $this->assertFalse($user->allowsAll([$readPosts, $writePosts]));
        $user->assignPermission($writePosts);
        $this->assertTrue($user->fresh()->allowsAll([$readPosts, $writePosts]));
    }

    public function test_allows_any_permissions_checking_through_role()
    {
        $readPosts = Permission::create(['name' => 'read:posts']);
        $writePosts = Permission::create(['name' => 'write:posts']);
        $editor = Role::create(['name' => 'editor'])->assignPermission($readPosts);
        $user = $this->makeUser();
        $this->assertFalse($user->allowsAny([$readPosts, $writePosts]));
        $user->assignRole($editor);
        $this->assertTrue($user->fresh()->allowsAny([$readPosts, $writePosts]));
    }

    public function test_allows_any_permissions_checking_through_direct_assignment()
    {
        $readPosts = Permission::create(['name' => 'read:posts']);
        $writePosts = Permission::create(['name' => 'write:posts']);
        $user = $this->makeUser();
        $this->assertFalse($user->allowsAny([$readPosts, $writePosts]));
        $user->assignPermission($readPosts);
        $this->assertTrue($user->fresh()->allowsAny([$readPosts, $writePosts]));
    }
}