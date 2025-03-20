<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Org\Organization;
use App\Models\Org\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin role and permissions
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $permissions = [
            'view users',
            'create users',
            'edit users',
            'delete users',
            'manage roles',
            'manage organizations',
            'manage teams'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        $adminRole->givePermissionTo($permissions);

        // Create admin user and assign role
        $this->admin = User::factory()->create(['email' => 'admin@example.com']);
        $this->admin->assignRole('admin');
    }

    #[Test]
    public function it_can_list_users()
    {
        $users = User::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index'));

        $response->assertStatus(200)
            ->assertInertia(fn ($page) => $page
                ->component('Admin/Users/Index')
                ->has('users', 13) // 3 created + 1 admin
            );
    }

    #[Test]
    public function it_can_show_user_details()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.show', $user));

        $response->assertStatus(200)
            ->assertInertia(fn ($page) => $page
                ->component('Admin/Users/Show')
                ->has('user')
                ->where('user.id', $user->id)
                ->has('roles')
            );
    }

    #[Test]
    public function it_can_create_user()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), $userData);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }

    #[Test]
    public function it_validates_required_fields_when_creating_user()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), []);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }

    #[Test]
    public function it_validates_unique_email_when_creating_user()
    {
        $existingUser = User::factory()->create(['email' => 'test@example.com']);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

        $response->assertSessionHasErrors('email');
    }

    #[Test]
    public function it_can_update_user()
    {
        $user = User::factory()->create();
        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.users.update', $user), $updateData);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }

    #[Test]
    public function it_validates_unique_email_when_updating_user()
    {
        $user1 = User::factory()->create(['email' => 'test1@example.com']);
        $user2 = User::factory()->create(['email' => 'test2@example.com']);

        $response = $this->actingAs($this->admin)
            ->put(route('admin.users.update', $user1), [
                'name' => 'Updated Name',
                'email' => 'test2@example.com',
            ]);

        $response->assertSessionHasErrors('email');
    }

    #[Test]
    public function it_can_delete_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.users.destroy', $user));

        $response->assertRedirect(route('admin.users.index'));
        $this->assertSoftDeleted($user);
    }

    #[Test]
    public function non_admin_users_cannot_access_user_management()
    {
        $regularUser = User::factory()->create();
        $routes = [
            'admin.users.index',
            'admin.users.create',
            'admin.users.store',
            'admin.users.show',
            'admin.users.edit',
            'admin.users.update',
            'admin.users.destroy',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($regularUser)
                ->get(route($route, ['user' => $regularUser]));

            $response->assertStatus(403);
        }
    }

    #[Test]
    public function it_can_assign_roles_to_user()
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'team-member', 'guard_name' => 'web']);

        $response = $this->actingAs($this->admin)
            ->put(route('admin.users.update', $user), [
                'name' => $user->name,
                'email' => $user->email,
                'roles' => [$role->name],
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertTrue($user->fresh()->hasRole($role->name));
    }

    #[Test]
    public function it_can_assign_organization_to_user()
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();

        $response = $this->actingAs($this->admin)
            ->put(route('admin.users.update', $user), [
                'name' => $user->name,
                'email' => $user->email,
                'organization_id' => $organization->id,
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertEquals($organization->id, $user->fresh()->organization_id);
    }

    #[Test]
    public function it_can_assign_team_to_user()
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();

        $response = $this->actingAs($this->admin)
            ->put(route('admin.users.update', $user), [
                'name' => $user->name,
                'email' => $user->email,
                'current_team_id' => $team->id,
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertEquals($team->id, $user->fresh()->current_team_id);
    }
}
