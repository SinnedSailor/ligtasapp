# Admin User Role Management System Setup

## Overview
The system now includes a complete admin user role management system with the following roles:
- **ADMIN**: System administrator with full access to manage users and roles
- **FOCAL**: Focal point user role
- **LGU**: Local Government Unit user role
- **PROVINCE**: Province user role

## Setup Instructions

### Step 1: Create the First Admin User
Before you can manage users and roles, you need to create an initial admin user. Visit the following URL:

```
http://yourapp.local/admin/create-first-admin
```

Fill in the admin details:
- First Name
- Last Name
- Username
- Email
- Province (optional)
- Municipality (optional)
- Password (must meet requirements below)
- Confirm Password

### Step 2: Password Requirements
The password must contain ALL of the following:
- Minimum 8 characters
- At least one uppercase letter (A-Z)
- At least one lowercase letter (a-z)
- At least one number (0-9)
- At least one special character (!@#$%^&*()_+\-=\[\]{}|;:,.<>?)

Example: `MyPassword123!`

### Step 3: Login with Admin Account
After creating the admin account, log in with your admin credentials.

### Step 4: Access User Management
Once logged in with admin credentials, you can access the user management panel at:

```
http://yourapp.local/admin/users
```

## Admin Features

### View All Users
The admin dashboard displays all users with their:
- Full name
- Email address
- Username
- Current role
- Admin status
- Province and Municipality

### Assign Roles to Users
1. Navigate to `/admin/users`
2. Click the "Assign Role" button next to any user
3. Select a role from the dropdown:
   - FOCAL
   - LGU
   - PROVINCE
4. Click "Assign Role" to save

### Grant/Revoke Admin Privileges
1. Navigate to `/admin/users`
2. Click "Make Admin" button to grant admin privileges to a regular user
3. Click "Revoke Admin" button to revoke admin privileges
4. Note: You cannot revoke your own admin privileges as a safety measure

## Database Schema

### Roles Table
```sql
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    created_at DATETIME,
    updated_at DATETIME
);
```

### Users Table (Updated)
The users table now includes:
- `role_id` (INT, FK to roles.id) - The user's assigned role
- `is_admin` (TINYINT) - Flag indicating if user has admin privileges

## Role Assignment Workflow

1. **First Admin Creation**: System allows creation of first admin at `/admin/create-first-admin`
2. **Admin Login**: Admin logs in with admin credentials
3. **User Management**: Admin can view all users at `/admin/users`
4. **Assign Role**: Admin can assign FOCAL, LGU, or PROVINCE roles to users
5. **Grant Admin**: Admin can grant admin privileges to other users
6. **Revoke Admin**: Admin can revoke admin privileges (except their own)

## API Endpoints (Admin Routes)

### Create First Admin
- **GET** `/admin/create-first-admin` - Display form to create first admin
- **POST** `/admin/store-first-admin` - Store the first admin user

### User Management
- **GET** `/admin/users` - Display all users (admin only)
- **POST** `/admin/assign-role` - Assign role to user (admin only)
- **POST** `/admin/grant-admin` - Grant admin privileges (admin only)
- **POST** `/admin/revoke-admin` - Revoke admin privileges (admin only)

## Session Variables
When a user logs in, the following session variables are set:
- `user_id` - User ID
- `email` - User email
- `username` - Username
- `first_name` - First name
- `last_name` - Last name
- `province` - User's province
- `municipality` - User's municipality
- `role_id` - User's role ID
- `is_admin` - Admin flag (0 or 1)
- `logged_in` - Login status (true/false)

## Security Notes

1. **Admin-Only Access**: All admin routes check for `is_admin` flag before allowing access
2. **Password Protection**: Passwords are hashed using `PASSWORD_DEFAULT` (bcrypt)
3. **Self-Protection**: Cannot revoke own admin privileges
4. **First Admin Safeguard**: First admin creation is only allowed when no admin exists
5. **Role Database**: Roles are managed from the database (ADMIN, FOCAL, LGU, PROVINCE)

## File Changes

### Created Files
- `app/Models/RoleModel.php` - Role model for managing roles
- `app/Controllers/Admin.php` - Admin controller for user and role management
- `app/Views/admin/users.php` - User management interface
- `app/Views/admin/create_first_admin.php` - First admin creation form
- `app/Database/Migrations/2026-02-05-101000_CreateRolesTable.php` - Create roles table
- `app/Database/Migrations/2026-02-05-102000_AddRoleToUsersTable.php` - Add role columns

### Modified Files
- `app/Models/UserModel.php` - Added `role_id` and `is_admin` to allowedFields
- `app/Controllers/Auth.php` - Added role_id and is_admin to session
- `app/Config/Routes.php` - Added admin routes

## Testing the System

1. Visit `http://yourapp.local/admin/create-first-admin`
2. Create an admin user with valid credentials
3. Log in with the admin credentials
4. Navigate to `http://yourapp.local/admin/users`
5. Test assigning roles to existing users
6. Test granting/revoking admin privileges to other users

## Troubleshooting

**Issue**: "Admin user already exists" message when creating first admin
- **Solution**: An admin user has already been created. Log in with existing admin credentials.

**Issue**: Cannot access admin panel after logging in
- **Solution**: Verify your account has `is_admin` flag set to 1. Contact current admin for privileges.

**Issue**: Foreign key constraint error during migration
- **Solution**: Ensure roles table is created before users table relationship is added.

## Next Steps

1. Create additional admin users for redundancy
2. Implement role-based access control (RBAC) for different views
3. Add role-based filters to user actions
4. Create an audit log for admin actions
5. Add email notifications for role assignments
