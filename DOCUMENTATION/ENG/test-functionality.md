# Functionality Testing Procedure

This guide explains how to test the Cash extension functionality through the test endpoint.

## Prerequisites

- phpBB forum installed and running
- Cash extension installed and activated
- Administrator privileges to access test features

## Testing Procedure

1. **Log in** to the forum with an administrator account

2. **Open** the following URL in your browser:
   ```
   https://your-domain.com/app.php/cash/testapi
   ```
   Replace `your-domain.com` with your forum's URL.

3. **Verify** the result:
   - If the endpoint is configured correctly, you'll see a JSON output with test results
   - If you get a 404 error, verify the URL is correct and the extension is installed
   - If you get a permission error, ensure you're logged in as an administrator

4. **Interpret** the results:
   - `status`: Indicates if the test was successful (success/error)
   - `messages`: Contains details of each test operation performed
   - `data`: Additional details about the operations performed

## Automated Tests

Automated tests are also available and can be run via command line:

```bash
# Run all tests
php ../../phpBB/vendor/bin/phpunit ../../ext/marcozp/cash/tests/

# Run only API tests
php ../../phpBB/vendor/bin/phpunit ../../ext/marcozp/cash/tests/test_api.php

# Run only web interface tests
php ../../phpBB/vendor/bin/phpunit ../../ext/marcozp/cash/tests/test_api_web.php
```

## Troubleshooting

### Endpoint not found (404)
- Verify the URL is correct
- Check that the extension is installed and enabled
- Clear phpBB cache

### Permission error
- Make sure you're logged in as an administrator
- Verify user permissions in the administration panel

### Test errors
- Check phpBB error logs
- Verify the database has been updated correctly
- Check that all dependencies are installed

## Notes

- Tests do not modify existing data in the database
- Temporary test data is used and removed after execution
- For persistent issues, consult the official documentation or open an issue in the official repository
