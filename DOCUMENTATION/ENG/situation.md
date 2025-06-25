# Cash Points Extension Analysis

## System Registration
- **Main File**: `ext.php`
- **Class**: `marcozp\cash\ext`
- **Key Methods**:
  - `is_enableable()`: Checks phpBB compatibility
  - `enable_step()`: Creates tables and settings
  - `purge_step()`: Removes tables

## Migration Procedures

### `install_acp_module.php`
- Adds ACP module for settings
- Sets extension version (1.0.0)
- Dependencies: `phpbb\db\migration\data\v330\v330`

### `add_manage_users_module.php`
- Adds ACP module for user management
- Dependencies: `install_acp_module`

## Database Structure

### Main Tables
1. `phpbb_cash_points`
   - `user_id` (primary key)
   - `points_total`
   - `points_today`
   - `last_activity`

2. `phpbb_cash_logs`
   - `log_id` (auto increment)
   - `user_id`
   - `log_time`
   - `log_type`
   - `log_points`
   - `log_data`

3. `phpbb_cash_settings`
   - `setting_name` (primary key)
   - `setting_value`

## Default Settings
```yaml
points_per_post: 10
points_per_topic: 15
points_per_poll_vote: 5
points_per_page_view: 1
max_daily_points: 100
show_billboard_link: 1
```

## Directory Structure
- `ext/
  └── marcozp/
      └── cash/
          ├── add_manage_users.php    # Script for adding user management
          ├── clean_tables.php        # Table cleanup script
          ├── composer.json           # Composer configuration
          ├── database_structure.md   # Database structure documentation
          ├── ext.php                 # Main extension file
          ├── installer.php           # Installation script
          ├── situation.md           # This documentation file
          ├── acp/                    # ACP modules
          │   ├── main_info.php      # ACP module information
          │   └── main_module.php    # ACP module implementation
          ├── adm/                    # Administration files
          │   └── style/
          │       ├── acp_cash_manage_users.html  # User management template
          │       └── acp_cash_settings.html   # Extension settings template
          ├── config/                 # Configuration files
          │   ├── routing.yml        # Routing configuration
          │   └── services.yml       # Services configuration
          ├── controller/             # Controllers
          │   ├── acp_controller.php  # ACP Controller (User points management)
          │   └── billboard_controller.php  # Points board controller
          ├── event/                  # Event listeners
          │   └── main_listener.php   # Extension event handlers
          ├── language/               # Language files
          │   ├── en/                 # English
          │   │   ├── acp/
          │   │   │   └── cash_common.php  # ACP texts in English
          │   │   ├── common.php      # Common texts in English
          │   │   └── info_acp_cash.php  # ACP information in English
          │   └── it/                 # Italian
          │       ├── acp/
          │       │   └── cash_common.php  # ACP texts in Italian
          │       ├── common.php      # Common texts in Italian
          │       └── info_acp_cash.php  # ACP information in Italian
          ├── migrations/             # Database migrations
          │   ├── add_manage_users_module.php  # Adds user management module
          │   └── install_acp_module.php       # Installs ACP module
          ├── service/                # Business logic
          │   └── points_manager.php  # User points management logic
          └── styles/                 # Styles and templates
              └── all/
                  └── template/event/
                      ├── memberlist_view_user_statistics_after.html  # Template for points display in user profile
                      ├── overall_header_navigation_append.html       # Navigation menu link addition
                      └── viewtopic_body_postrow_custom_fields_after.html  # Points display in forum posts

## Current Extension Status

## Implemented Features

### User Points Management
- Add and subtract user points
- Daily points update cron job
- User leaderboard
- Points management via ACP

### Administration Panel (ACP)
- User points management interface
- Paginated user leaderboard
- Form for adding/subtracting points
- User search with autocomplete

### Recent Improvements (June 2025)

#### User Interface
- Resized user leaderboard to 50% width
- Improved readability with striped rows
- Added quick edit points icon
- Enhanced pagination with next/previous navigation
- Number formatting for better readability

#### Functionality
- Paginated user leaderboard (20 users per page)
- Quick edit points button with auto-fill
- Users sorted by descending score
- Disabled state handling for navigation buttons

#### Performance
- Optimized pagination queries
- Reduced number of database queries
- Improved cache management

## Upcoming Developments
- [ ] Add user search filters
- [ ] Export leaderboard to CSV/PDF
- [ ] Advanced points statistics
- [ ] Detailed transaction logs

## Notes
- The extension is currently under active development
- Documentation is constantly being updated
- Regular backups are recommended during updates
