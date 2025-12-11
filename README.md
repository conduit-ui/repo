# Conduit UI - Repos

A clean, expressive GitHub repository management package built on [conduit-ui/connector](https://github.com/conduit-ui/connector).

## Installation

```bash
composer require conduit-ui/repos
```

## Configuration

Publish the configuration file (optional):

```bash
php artisan vendor:publish --tag=repos-config
```

## Usage

### Finding a Repository

```php
use ConduitUI\Repos\Facades\Repos;

$repo = Repos::find('laravel/framework');

echo $repo->name; // "framework"
echo $repo->fullName; // "laravel/framework"
echo $repo->stargazersCount; // 75000+
```

### Querying Repositories

**User Repositories:**

```php
$repos = Repos::forUser('taylorotwell')->get();

foreach ($repos as $repo) {
    echo "{$repo->name} - {$repo->description}\n";
}
```

**Organization Repositories:**

```php
$repos = Repos::forOrg('laravel')->get();
```

**Authenticated User Repositories:**

```php
$repos = Repos::forAuthenticatedUser()->get();
```

### Advanced Queries

```php
$repos = Repos::query()
    ->user('taylorotwell')
    ->visibility('public')
    ->type('owner')
    ->sort('updated')
    ->direction('desc')
    ->perPage(50)
    ->get();
```

### Creating Repositories

**User Repository:**

```php
$repo = Repos::create([
    'name' => 'my-new-repo',
    'description' => 'A test repository',
    'private' => false,
    'auto_init' => true,
]);
```

**Organization Repository:**

```php
$repo = Repos::create([
    'org' => 'my-org',
    'name' => 'org-repo',
    'private' => true,
]);
```

### Updating Repositories

```php
$repo = Repos::update('owner/repo', [
    'description' => 'Updated description',
    'homepage' => 'https://example.com',
    'has_issues' => true,
]);
```

### Deleting Repositories

```php
Repos::delete('owner/repo');
```

### Repository Details

**Branches:**

```php
$branches = Repos::branches('laravel/framework');

foreach ($branches as $branch) {
    echo "{$branch->name} - Protected: " . ($branch->protected ? 'Yes' : 'No') . "\n";
}
```

**Releases:**

```php
$releases = Repos::releases('laravel/framework');

foreach ($releases as $release) {
    echo "{$release->tagName} - {$release->name}\n";
    echo "Assets: " . count($release->assets) . "\n";
}
```

**Collaborators:**

```php
$collaborators = Repos::collaborators('owner/repo');

foreach ($collaborators as $collaborator) {
    echo "{$collaborator->login} - Admin: " . ($collaborator->permissions->admin ? 'Yes' : 'No') . "\n";
}
```

**Topics:**

```php
$topics = Repos::topics('laravel/framework');
// ['php', 'framework', 'laravel']
```

**Languages:**

```php
$languages = Repos::languages('laravel/framework');
// ['PHP' => 12345678, 'JavaScript' => 123456, ...]
```

## Data Transfer Objects

The package provides clean, immutable DTOs for all GitHub repository data:

- `Repository` - Complete repository information
- `Branch` - Branch details with commit information
- `Release` - Release information with assets
- `Collaborator` - Collaborator with permissions
- `Owner` - Repository owner details
- `License` - License information
- `Permissions` - Access permissions

All DTOs provide `fromArray()` and `toArray()` methods for easy serialization.

## Testing

```bash
composer test
```

## Code Quality

```bash
composer format    # Fix code style
composer analyse   # Run static analysis
```

## License

MIT License. See [LICENSE](LICENSE) for details.
