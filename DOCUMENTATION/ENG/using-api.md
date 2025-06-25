# Points Management API Integration Guide

## Table of Contents
1. [Introduction](#introduction)
2. [Installation](#installation)
3. [Basic Usage](#basic-usage)
4. [Available Methods](#available-methods)
5. [Error Handling](#error-handling)
6. [Practical Examples](#practical-examples)
7. [Security](#security)

## Introduction
This document describes how to integrate the Points Management System (Cash) into other phpBB extensions through a dedicated API. The API provides methods to manage user points securely and in a controlled manner.

## Installation
The API is included in the Cash extension and is registered automatically. Ensure that:
1. The Cash extension is installed and enabled
2. The extension using the API has `marcozp/cash` as a dependency in `composer.json`

## Basic Usage
To use the API in another extension:

```php
// Get the API instance
$pointsApi = $phpbb_container->get('marcozp.cash.api.points');

// Example: Add points to a user
$pointsApi->addPoints($userId, 10, 'extension_name.reward', 'Reward reason');
```

## Available Methods

### `getUserPoints(int $userId): int`
Returns a user's total score.

**Parameters:**
- `$userId`: User ID

**Returns:**
- `int` User's total points

---

### `addPoints(int $userId, int $points, string $action, string $data = ''): bool`
Adds or subtracts points from a user.

**Parameters:**
- `$userId`: User ID
- `$points`: Points to add (negative values to subtract)
- `$action`: Unique action identifier (e.g., 'forum.post', 'shop.purchase')
- `$data`: Optional additional data

**Returns:**
- `bool` True on success

**Exceptions:**
- `NotEnoughPointsException` if user doesn't have enough points
- `UserNotFoundException` if user doesn't exist

---

### `canUserModifyPoints(int $modifierId, int $targetUserId): bool`
Checks if a user can modify another user's points.

**Parameters:**
- `$modifierId`: ID of the user requesting the modification
- `$targetUserId`: ID of the target user

**Returns:**
- `bool` True if the user can modify points

## Error Handling
The API uses specific exceptions for error handling:

```php
try {
    $pointsApi->addPoints($userId, -50, 'shop.purchase');
} catch (\marcozp\cash\service\api\exception\NotEnoughPointsException $e) {
    // Handle insufficient points error
} catch (\marcozp\cash\service\api\exception\UserNotFoundException $e) {
    // Handle user not found
}
```

## Practical Examples

### 1. Post Reward System
```php
// In a listener for submit_post_end event
public function on_submit_post($event) {
    $postData = $event['data'];
    $pointsApi = $this->container->get('marcozp.cash.api.points');
    
    // Add 10 points for a new post
    if ($event['mode'] == 'post') {
        $pointsApi->addPoints(
            $postData['poster_id'],
            10,
            'forum.new_post',
            'post_id:' . $postData['post_id']
        );
    }
}
```

### 2. Virtual Shop
```php
public function purchaseItem($userId, $itemId, $itemCost) {
    $pointsApi = $this->container->get('marcozp.cash.api.points');
    
    try {
        // Deduct points
        $pointsApi->addPoints($userId, -$itemCost, 'shop.purchase', 'item:' . $itemId);
        
        // Complete purchase
        return $this->completePurchase($userId, $itemId);
        
    } catch (\marcozp\cash\service\api\exception\NotEnoughPointsException $e) {
        throw new \RuntimeException('Not enough points to complete the purchase');
    }
}
```

## Security
1. **Input Validation**: All inputs are validated
2. **Authorization**: Always verify permissions with `canUserModifyPoints()`
3. **Logging**: All operations are logged
4. **Transactions**: Critical operations are performed in atomic transactions

## Best Practices
1. **Unique Prefixes**: Use unique prefixes for your actions (e.g., `your_extension.action`)
2. **Error Handling**: Always implement exception handling
3. **Performance**: Avoid point operations in loops or batch processes
4. **Testing**: Always test behavior with users of different permission levels

## Support
For issues or questions, please open an issue in the official Cash extension repository.
