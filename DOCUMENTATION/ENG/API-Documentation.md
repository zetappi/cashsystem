# Points Management API Documentation

## Table of Contents
1. [Introduction](#introduction)
2. [Prerequisites](#prerequisites)
3. [Getting the Service Instance](#getting-the-service-instance)
4. [Available Operations](#available-operations)
   - [Get User Points](#get-user-points)
   - [Add Points](#add-points)
   - [Remove Points](#remove-points)
   - [Check Permissions](#check-permissions)
5. [Common Use Cases](#common-use-cases)
   - [New Post Reward](#new-post-reward)
   - [Shop Purchase](#shop-purchase)
6. [Best Practices](#best-practices)
7. [Action Naming Conventions](#action-naming-conventions)
8. [Complete Extension Example](#complete-extension-example)
9. [Troubleshooting](#troubleshooting)

## Introduction
This documentation explains how to integrate the Points Management API into other phpBB extensions, allowing for easy and secure user points management.

## Prerequisites
- `marcozp/cash` extension installed and active
- Appropriate permissions to modify user points

## Getting the Service Instance

```php
global $phpbb_container;

// Verify the service exists
if ($phpbb_container->has('marcozp.cash.api.points')) {
    $pointsApi = $phpbb_container->get('marcozp.cash.api.points');
} else {
    // Handle error
    trigger_error('Cash extension not available');
}
```

## Available Operations

### Get User Points

```php
try {
    $userId = 123; // User ID
    $points = $pointsApi->getUserPoints($userId);
} catch (\marcozp\cash\service\api\exception\UserNotFoundException $e) {
    // Handle error
}
```

### Add Points

```php
try {
    $pointsApi->addPoints(
        $userId,           // User ID
        10,                // Points to add (positive number)
        'action.identifier', // Action identifier
        'Additional details' // Optional
    );
} catch (\Exception $e) {
    // Handle error
}
```

### Remove Points

```php
try {
    // To remove points, use a negative number
    $pointsApi->addPoints($userId, -5, 'action.identifier', 'Details');
} catch (\marcozp\cash\service\api\exception\NotEnoughPointsException $e) {
    // Not enough points
}
```

### Check Permissions

```php
if ($pointsApi->canUserModifyPoints($modifierId, $targetUserId)) {
    // Operation allowed
}
```

## Common Use Cases

### New Post Reward

```php
// In a listener for 'core.submit_post_end' event
public function onPostSubmit($event)
{
    global $user, $phpbb_container;
    
    if (($pointsApi = $this->getPointsApi()) !== null) {
        try {
            $pointsApi->addPoints(
                $user->data['user_id'], 
                5, 
                'post.created', 
                'Post #' . $event['data']['post_id']
            );
        } catch (\Exception $e) {
            error_log('Error adding points: ' . $e->getMessage());
        }
    }
}
```

### Shop Purchase

```php
public function purchaseItem($userId, $itemId, $itemName, $cost)
{
    if (($pointsApi = $this->getPointsApi()) === null) {
        throw new \Exception('Points system not available');
    }
    
    try {
        // Check available points
        $currentPoints = $pointsApi->getUserPoints($userId);
        if ($currentPoints < $cost) {
            throw new \Exception('Insufficient points');
        }
        
        // Deduct points
        $pointsApi->addPoints(
            $userId, 
            -$cost, 
            'shop.purchase', 
            "Purchase: $itemName (ID: $itemId)"
        );
        
        return true;
    } catch (\Exception $e) {
        error_log('Purchase error: ' . $e->getMessage());
        throw $e;
    }
}
```

## Best Practices

1. **Error Handling**: Always implement try-catch blocks
2. **Logging**: Log important operations
3. **Security**: Verify permissions
4. **Performance**: Minimize API calls
5. **Documentation**: Document actions in the `$data` parameter

## Action Naming Conventions

Use the `type.action` format:
- `post.created`
- `topic.replied`
- `user.registered`
- `shop.purchase`
- `moderator.award`

## Complete Extension Example

```php
namespace vendor\yourextension\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
    protected $pointsApi;
    protected $container;
    
    public function __construct(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    static public function getSubscribedEvents()
    {
        return [
            'core.user_setup' => 'load_language_on_setup',
            'core.submit_post_end' => 'on_post_submit',
        ];
    }
    
    protected function getPointsApi()
    {
        if ($this->pointsApi === null && $this->container->has('marcozp.cash.api.points')) {
            $this->pointsApi = $this->container->get('marcozp.cash.api.points');
        }
        return $this->pointsApi;
    }
    
    public function on_post_submit($event)
    {
        if (($pointsApi = $this->getPointsApi()) === null) {
            return;
        }
        
        try {
            $pointsApi->addPoints(
                $event['data']['poster_id'],
                5,
                'post.created',
                'Post #' . $event['data']['post_id']
            );
        } catch (\Exception $e) {
            error_log('Error in on_post_submit: ' . $e->getMessage());
        }
    }
    
    public function load_language_on_setup($event)
    {
        $lang_set_ext = $event['lang_set_ext'];
        $lang_set_ext[] = [
            'ext_name' => 'vendor/yourextension',
            'lang_set' => 'common',
        ];
        $event['lang_set_ext'] = $lang_set_ext;
    }
}
```

## Troubleshooting

### Common Issues

1. **Service Not Found**
   - Ensure the Cash extension is installed and enabled
   - Clear the cache after installation

2. **Permission Denied**
   - Verify the user has the correct permissions
   - Check the `canUserModifyPoints()` method

3. **Points Not Updating**
   - Check for exceptions in the error log
   - Verify the user exists and is not the anonymous user

4. **Negative Points**
   - Ensure you're not allowing negative points unless intended
   - Check for race conditions in your code

### Debugging

Enable debug mode in phpBB and check the following:
1. Error logs
2. Database queries
3. User permissions
4. Extension dependencies

For additional help, refer to the official phpBB documentation or open an issue on the extension's GitHub repository.
