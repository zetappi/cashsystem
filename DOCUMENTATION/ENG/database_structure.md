# Database Structure - Cash Points System

This document describes the database structure used by the Cash Points System extension for phpBB. This information is provided to facilitate integration with other extensions that may want to interact with the points system.

## Tables

The extension uses three main tables:

### 1. {table_prefix}cash_points

This table stores the total and daily points of each user.

| Column | Type | Default | Description |
|--------|------|---------|-------------|
| user_id | UINT | 0 | User ID (primary key) |
| points_total | UINT | 0 | Total points accumulated by the user |
| points_today | UINT | 0 | Points accumulated by the user today |
| last_activity | TIMESTAMP | 0 | Timestamp of the last activity that generated points |

### 2. {table_prefix}cash_logs

This table logs all point transactions (additions and subtractions).

| Column | Type | Default | Description |
|--------|------|---------|-------------|
| log_id | UINT | auto_increment | Log ID (primary key) |
| user_id | UINT | 0 | User ID |
| log_time | TIMESTAMP | 0 | Transaction timestamp |
| log_type | VARCHAR(32) | '' | Transaction type (post, topic, poll_vote, page_view, admin_add, admin_subtract, etc.) |
| log_points | INT(11) | 0 | Number of points added or subtracted (negative value for subtractions) |
| log_data | TEXT | '' | Additional transaction data (e.g., post ID, topic ID, etc.) |

Indexes:
- user_id (INDEX)
- log_time (INDEX)
- log_type (INDEX)

### 3. {table_prefix}cash_settings

This table stores the configuration settings for the points system.

| Column | Type | Default | Description |
|--------|------|---------|-------------|
| setting_name | VARCHAR(255) | '' | Setting name (primary key) |
| setting_value | VARCHAR(255) | '' | Setting value |

## Default Settings

The following settings are created during extension installation and can be configured via ACP:

| Setting | Default Value | Description |
|---------|---------------|-------------|
| points_per_post | 10 | Points awarded for each new post |
| points_per_topic | 15 | Points awarded for each new topic |
| points_per_poll_vote | 5 | Points awarded for each poll vote |
| points_per_page_view | 1 | Points awarded for each page view |
| max_daily_points | 100 | Maximum points a user can earn in a day |
| show_billboard_link | 1 | Show (1) or hide (0) the leaderboard link in the navigation bar |

## Integration with Other Extensions

### Accessing User Points

To access a user's points, you can use the `points_manager` service:

```php
// Get user points
$user_data = $points_manager->get_user_points($user_id);
$points_total = $user_data['points_total'];
```

### Adding Points

To add points to a user:

```php
// Add 50 points to the user
$points_manager->add_points($user_id, 50, 'custom_action', 'Additional data');
```

### Subtracting Points

To subtract points from a user:

```php
// Subtract 25 points from the user
$points_manager->subtract_points($user_id, 25, 'custom_action', 'Additional data');
```

### Getting the Leaderboard

To get the ranking of top users:

```php
// Get top 10 users
$top_users = $points_manager->get_top_users(10);
```

## Important Notes

1. All point operations are logged in the `{table_prefix}cash_logs` table.
2. The system respects the daily point limit configured in `max_daily_points`.
3. Anonymous users (ANONYMOUS) are excluded from the points system.
4. Daily points are automatically reset when a user logs in on a new day.

---

Document generated on April 11, 2025
