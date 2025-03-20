# Design Document: User Management, Roles, Permissions, and Rank System

## 1. Overview

This document details the design and implementation of a user management system built on Laravel 12. The system supports:

- **Roles & Permissions**: Defines what actions a user can perform (e.g., add users, reset users, assign roles).
- **Rank System**: Defines the data visibility scope for users.
    - **Admin**: Can view all organizations and all users.
    - **Org Leader**: Can view only the organization they lead.
    - **Captain**: Can view details only within their team.
    - **Team Member**: Visibility is determined by historical scope. If a team member moves between captains, each captain can only see actions taken during the team member’s tenure within that captain’s team.

## 2. System Architecture

### 2.1. Core Concepts

- **Roles & Permissions**: Define what actions a user is authorized to perform.
- **Ranks (Visibility Scopes)**: Determine the boundaries of data a user can access. This is independent of the actions they can perform.
- **Historical Tracking**: Particularly for users who change teams, ensuring that captains only see actions performed during the team member’s time under their supervision.

### 2.2. Architectural Layers

- **Presentation Layer**: Blade views or Inertia/Vue/React components that consume data.
- **Application Layer**: Controllers, Services, and Middleware that handle requests and enforce both permissions and rank scopes.
- **Data Layer**: Eloquent models and database schema managing relationships between users, teams, organizations, roles, and rank histories.

## 3. Database Schema Design

### 3.1. Tables

1. **users**
    - `id`, `name`, `email`, `password`, etc.
    - Foreign keys: `organization_id` (nullable, if the user is tied to an organization) and possibly `current_team_id` if the user is part of a team.

2. **organizations**
    - `id`, `name`, `description`, etc.

3. **teams**
    - `id`, `name`, `organization_id`
    - A team belongs to an organization.

4. **role_user** (or using Spatie's tables)
    - Pivot table linking users to roles.

5. **permissions** (if not using a package, otherwise Spatie’s tables)
    - Define various permissions.

6. **rank_histories** (or team_assignment_histories)
    - `id`, `user_id`, `captain_id` (or `team_leader_id`), `team_id`, `start_date`, `end_date` (nullable if current)
    - This table tracks which team (and hence which captain) the team member belonged to over time.

7. **audit_logs** (optional but recommended)
    - To store actions performed by users, including a reference to which scope (rank) was in effect when the action was taken.

### 3.2. Relationships

- **User Model**
    - Belongs to an Organization.
    - Belongs to a Team (current team).
    - Has many `rankHistories` to track team assignments.

- **Organization Model**
    - Has many Users.
    - Has many Teams.

- **Team Model**
    - Belongs to an Organization.
    - Has many Users.
    - Has one Captain (this could be a role assignment or a dedicated column).

- **RankHistory Model**
    - Belongs to a User.
    - Belongs to a Team.
    - Belongs to a Captain (or, via the team, the captain can be derived).

## 4. Application Flow and Enforcement

### 4.1. Authentication & Authorization

- **Authentication**: Use Laravel’s built-in systems (Laravel Breeze, Jetstream, or Fortify).
- **Roles & Permissions**: Managed via a package like Spatie's or a custom solution.
- **Middleware**: Create middleware that checks both the user’s permissions (what they can do) and their rank (what they can see). For example, a captain's middleware should filter queries based on `rank_histories` to ensure that only records made during the team member’s tenure under that captain are returned.

### 4.2. Controller Logic

- **UserController**: Methods for adding, editing, and listing users.
    - Filter users based on the authenticated user’s rank:
        - **Admin**: No additional filters.
        - **Org Leader**: Filter users to those within their organization.
        - **Captain**: Filter users by team and narrow by the period during which a team member was assigned to that captain.

- **TeamController/OrgController**: Handle team or organization-specific actions with similar filtering logic.

### 4.3. Service Layer (Optional)

- Encapsulate rank and scope filtering logic in services (e.g., `VisibilityService` with methods like `getVisibleUsersForCaptain($captainId)`).

### 4.4. Event Logging and Historical Data

- **Rank History Updates**: Update `rank_histories` every time a team member’s assignment changes.
- **Audit Logs**: Log critical actions with metadata that includes the effective rank or scope at the time of the action.

## 5. Implementation Details

### 5.1. Models and Relationships

Define Eloquent relationships based on the schema. For example, in the `User` model:

```php
class User extends Authenticatable
{
    // Organization relation
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    // Current team relation (if applicable)
    public function team()
    {
        return $this->belongsTo(Team::class, 'current_team_id');
    }

    // Rank history (team assignments)
    public function rankHistories()
    {
        return $this->hasMany(RankHistory::class);
    }

    // Roles & permissions (using Spatie/laravel-permission traits)
    use HasRoles;
}

### 5.2. Middleware for Scope Enforcement

Create middleware (e.g., `CheckVisibilityScope`) that:

- Reads the authenticated user’s role and rank.
- Applies query filters to restrict data based on organization, team, or historical period.

**Example pseudocode:**

```php
public function handle($request, Closure $next)
{
    $user = auth()->user();

    // If the user is a captain, modify the query to only include records 
    // from the period when team members were assigned under this captain.
    if ($user->hasRole('captain')) {
        // Example: using a query scope or modifying the repository query
        // $query->whereHas('rankHistories', function ($q) use ($user) {
        //     $q->where('captain_id', $user->id)
        //       ->whereNull('end_date'); // or include a date range filter
        // });
    }

    return $next($request);
}
```

### 5.3. Handling Dynamic Team Memberships

When a team member switches teams:
- **Update the `rank_histories` table:**
  - Set the `end_date` for the current assignment.
  - Create a new record for the new assignment.
- When querying historical data, join with `rank_histories` to filter based on the effective period of the team member’s assignment.

### 5.4. API & Frontend Considerations

- **APIs:** Build endpoints (REST or GraphQL) that return data filtered by visibility scope. The backend must enforce these filters.
- **Frontend:** Use UI components (Laravel Blade, Inertia, Vue, React, etc.) that display only the data the user is allowed to see.

## 6. Security Considerations

- **Authorization Checks:** Ensure that every controller or service method applies both permission and rank validations.
- **Data Integrity:** Implement database constraints (foreign keys, cascading updates/deletes) to maintain consistency between users, teams, and organizations.
- **Audit Logging:** Maintain logs for critical actions, especially those that modify user assignments or roles.

## 7. Summary

This design leverages Laravel 12’s robust ecosystem to separate what a user can *do* (via roles and permissions) from what a user can *see* (via the rank system). By tracking team assignments and historical data in a dedicated table, the system supports complex scenarios such as a team member switching teams. Middleware and service layers ensure consistent enforcement of both data visibility and action permissions across the application.

This modular approach facilitates future extensions or modifications—such as adding new roles or adjusting visibility rules—without overhauling the entire architecture.
