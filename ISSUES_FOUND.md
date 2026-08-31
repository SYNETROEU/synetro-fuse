# Deeper Issues Found

## Critical Issues (Security/Data Loss)

### 1. WebhookManager Signature Mismatch (Security)
**File**: `src/Webhooks/WebhookManager.php`
**Issue**: Signature is computed with `$event.json_encode($payload)` but `verify()` expects just `$payload`
```php
// send() - signing with: $event + payload
$signature = hash_hmac('sha256', $event.json_encode($payload), $secret);

// verify() - verifying with: just payload
$expected = hash_hmac('sha256', $payload, $secret);
```
**Impact**: Webhook signature verification will always fail. Endpoints can't validate legitimate webhooks.
**Fix**: Make verify() consistent with send():
```php
public function verify(string $event, string $payload, string $signature, string $secret): bool
{
    $expected = hash_hmac('sha256', $event.$payload, $secret);
    return hash_equals($expected, $signature);
}
```

### 2. ImportExportManager CSV Escaping Issues
**File**: `src/ImportExport/ImportExportManager.php`
**Issue**: CSV export doesn't properly escape quotes and special characters
**Impact**: CSV data corruption when records contain commas or quotes

### 3. ConfigManager.all() Memory Issue
**File**: `src/Config/ConfigManager.php:52`
**Issue**: Loads ALL records into memory without pagination
```php
public function all(): Collection
{
    return ConfigModel::all()->mapWithKeys(...); // Unbounded query!
}
```
**Impact**: Memory exhaustion on large config datasets

## High Priority Issues (Data Consistency)

### 4. ResourceService Missing Transactions
**File**: `src/Resources/ResourceService.php`
**Issue**: create() and update() don't wrap in transactions
**Impact**: Partial saves if multiple models are being created

### 5. ImportExportManager.import() No Transactions
**File**: `src/ImportExport/ImportExportManager.php:11`
**Issue**: Bulk imports not wrapped in transactions
**Impact**: Partial imports on failure (no rollback)

### 6. ConfigManager.publish() No Error Handling
**File**: `src/Config/ConfigManager.php:56`
**Issue**: File operations without try/catch
```php
public function publish(): bool
{
    $target = config_path('fuse.php');
    if (file_exists($target)) {
        return false;
    }
    
    file_put_contents($target, $content); // Can throw silently
    return true;
}
```

## Medium Priority Issues (Validation/Edge Cases)

### 7. ImportExportManager.parseFile() Missing CSV Headers
**File**: `src/ImportExport/ImportExportManager.php:70`
**Issue**: CSV parsing doesn't skip header row properly
**Impact**: First row of data gets treated as headers

### 8. Missing Error Handling in ResourceService
**File**: `src/Resources/ResourceService.php`
**Issue**: No validation before mass assignment
**Impact**: Silent failures on protected attributes

## Test Coverage Gaps

- No tests for webhook signature verification
- No tests for CSV export with special characters
- No tests for config caching invalidation
- No tests for import/export error scenarios
- No tests for transaction rollbacks
