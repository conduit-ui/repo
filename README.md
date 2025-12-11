# Repository Governance at Scale

Stop manually auditing repository settings. Start enforcing compliance policies across your entire organization.

Audit, update, and manage repositories in bulk. Enforce branch protection, security policies, and team permissions across hundreds of repos with clean PHP code.

[![Latest Version](https://img.shields.io/packagist/v/conduit-ui/repo.svg?style=flat-square)](https://packagist.org/packages/conduit-ui/repo)
[![MIT License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/conduit-ui/repo.svg?style=flat-square)](https://packagist.org/packages/conduit-ui/repo)

## Installation

```bash
composer require conduit-ui/repo
```

## Why This Exists

Your organization has 200+ repositories. Some have branch protection disabled. Others allow force pushes to main. Security policies are inconsistent. This package gives you the tools to audit and enforce compliance at scale.

## Quick Start

```php
use ConduitUI\Repo\Repository;

// Find a single repository
$repo = Repository::find('owner/repo');

// Audit all org repositories
Repository::forOrganization('your-org')
    ->get()
    ->filter(fn($repo) => !$repo->branchProtectionEnabled())
    ->each(fn($repo) => $repo->enableBranchProtection('main'));

// Create a new repository
Repository::create('new-repo', [
    'description' => 'API service for user management',
    'private' => true,
    'auto_init' => true,
]);
```

## Core Features

### Repository Discovery

**Find by Name**
```php
$repo = Repository::find('owner/repo');
```

**List User Repositories**
```php
$repos = Repository::forUser('username')->get();
```

**List Organization Repositories**
```php
$repos = Repository::forOrganization('org-name')->get();
```

**List Authenticated User's Repos**
```php
$repos = Repository::forAuthenticatedUser()->get();
```

### Repository Creation

**Basic Creation**
```php
Repository::create('my-new-repo', [
    'description' => 'A new project',
    'private' => false,
]);
```

**Organization Repository**
```php
Repository::createForOrganization('org-name', 'repo-name', [
    'description' => 'Team project',
    'private' => true,
    'auto_init' => true,
    'gitignore_template' => 'PHP',
    'license_template' => 'mit',
]);
```

### Repository Updates

**Update Metadata**
```php
$repo = Repository::find('owner/repo');
$repo->update([
    'description' => 'Updated description',
    'homepage' => 'https://example.com',
    'private' => true,
    'has_issues' => true,
    'has_wiki' => false,
]);
```

### Repository Details

**Branches**
```php
$branches = $repo->branches();
$main = $repo->defaultBranch();
```

**Releases**
```php
$releases = $repo->releases();
$latest = $repo->latestRelease();
```

**Collaborators**
```php
$collaborators = $repo->collaborators();
```

**Topics (Tags)**
```php
$topics = $repo->topics();
```

**Languages**
```php
$languages = $repo->languages();
// ['PHP' => 75234, 'JavaScript' => 12456, ...]
```

### Repository Deletion

```php
Repository::find('owner/repo')->delete();
```

## Governance & Compliance

### Audit Branch Protection

```php
// Find repos without branch protection
Repository::forOrganization('your-org')
    ->get()
    ->filter(fn($repo) => !$repo->branchProtectionEnabled())
    ->each(function($repo) {
        echo "{$repo->name} is not protected!\n";
    });
```

### Enforce Security Policies

```php
// Disable force pushes across all repos
Repository::forOrganization('your-org')
    ->get()
    ->each(fn($repo) => $repo->update([
        'allow_force_pushes' => false,
        'delete_branch_on_merge' => true,
    ]));
```

### Standardize Repository Settings

```php
// Enforce consistent settings across org
$standardSettings = [
    'has_issues' => true,
    'has_wiki' => false,
    'has_projects' => false,
    'allow_squash_merge' => true,
    'allow_merge_commit' => false,
    'allow_rebase_merge' => false,
    'delete_branch_on_merge' => true,
];

Repository::forOrganization('your-org')
    ->get()
    ->each(fn($repo) => $repo->update($standardSettings));
```

### Archive Stale Repositories

```php
// Archive repos with no commits in 12 months
Repository::forOrganization('your-org')
    ->get()
    ->filter(fn($repo) => $repo->pushedAt < now()->subYear())
    ->each(fn($repo) => $repo->update(['archived' => true]));
```

## Bulk Operations

### Clone All Org Repositories

```php
Repository::forOrganization('your-org')
    ->get()
    ->each(function($repo) {
        $cloneUrl = $repo->cloneUrl;
        exec("git clone {$cloneUrl}");
    });
```

### Generate Org Report

```php
$report = Repository::forOrganization('your-org')
    ->get()
    ->map(fn($repo) => [
        'name' => $repo->name,
        'private' => $repo->private,
        'branch_protection' => $repo->branchProtectionEnabled(),
        'last_push' => $repo->pushedAt->diffForHumans(),
        'open_issues' => $repo->openIssuesCount,
        'language' => $repo->language,
    ]);

// Export to CSV, send to Slack, etc.
```

### Bulk Topic Management

```php
// Tag all PHP repos
Repository::forOrganization('your-org')
    ->get()
    ->filter(fn($repo) => $repo->language === 'PHP')
    ->each(fn($repo) => $repo->addTopics(['php', 'backend']));
```

## Data Objects

All responses return strongly-typed DTOs:

```php
$repo->id;                    // int
$repo->name;                  // string
$repo->fullName;              // string (owner/repo)
$repo->description;           // ?string
$repo->private;               // bool
$repo->fork;                  // bool
$repo->archived;              // bool
$repo->disabled;              // bool
$repo->owner;                 // Owner object
$repo->license;               // ?License object
$repo->language;              // ?string
$repo->defaultBranch;         // string
$repo->openIssuesCount;       // int
$repo->stargazersCount;       // int
$repo->watchersCount;         // int
$repo->forksCount;            // int
$repo->createdAt;             // Carbon instance
$repo->updatedAt;             // Carbon instance
$repo->pushedAt;              // Carbon instance
$repo->cloneUrl;              // string
$repo->sshUrl;                // string
$repo->branchProtectionEnabled(); // bool
```

## Usage Patterns

### Static API (Recommended)
```php
use ConduitUI\Repo\Repository;

$repo = Repository::find('owner/repo');
$repos = Repository::forOrganization('org')->get();
```

### Instance API
```php
use ConduitUI\Repo\RepositoryManager;

$manager = new RepositoryManager();
$repo = $manager->find('owner/repo');
```

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag="repo-config"
```

Set your GitHub token in `.env`:

```env
GITHUB_TOKEN=your-github-token
```

For organization-level operations, ensure your token has `repo` and `admin:org` scopes.

## Requirements

- PHP 8.2+
- GitHub personal access token or GitHub App
- For org operations: `admin:org` scope

## Testing

```bash
composer test
```

## Code Quality

```bash
composer format  # Fix code style
composer analyse # Run static analysis
```

## Related Packages

- [conduit-ui/issue](https://github.com/conduit-ui/issue) - Issue triage automation
- [conduit-ui/pr](https://github.com/conduit-ui/pr) - Pull request automation
- [conduit-ui/connector](https://github.com/conduit-ui/connector) - GitHub API transport layer

## Enterprise Support

Managing governance for 500+ repositories? Contact [jordan.woop@partridge.rocks](mailto:jordan.woop@partridge.rocks) for enterprise solutions including:

- Custom compliance reporting
- Automated policy enforcement
- Audit trail integration
- SOC2/ISO27001 compliance tooling
- Multi-organization management

## License

MIT License. See [LICENSE](LICENSE.md) for details.
