<?php
/**
 * Upstream Update Configuration
 *
 * Configuration for keeping the codebase in sync with the upstream repository.
 * This allows forks/clones to easily pull updates from the main repository
 * while preserving local customizations.
 *
 * @package    App
 * @subpackage Config
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

return array(
	/**
	 * -------------------------------------------------------------------------
	 *  Repository Settings
	 * -------------------------------------------------------------------------
	 *
	 *  Configuration for the upstream repository.
	 *
	 */

	'repository' => array(
		// URL of the upstream repository
		'url' => 'https://github.com/ruelasalva/base.git',

		// Branch to sync from
		'branch' => 'main',

		// Name for the remote in git config
		'remote_name' => 'upstream',
	),

	/**
	 * -------------------------------------------------------------------------
	 *  Excluded Paths
	 * -------------------------------------------------------------------------
	 *
	 *  Paths that should NOT be overwritten when syncing from upstream.
	 *  These typically include tenant-specific configurations and customizations.
	 *
	 */

	'exclude' => array(
		// Environment-specific configurations
		'fuel/app/config/development/',
		'fuel/app/config/production/',
		'fuel/app/config/staging/',

		// Tenant-specific packages
		'fuel/packages_tenant/',

		// Local database configuration
		'fuel/app/config/db.php',

		// Local cryptography keys
		'fuel/app/config/crypt.php',

		// Custom themes
		'fuel/app/themes/',

		// Local logs and cache
		'fuel/app/logs/',
		'fuel/app/cache/',
	),

	/**
	 * -------------------------------------------------------------------------
	 *  Sync Settings
	 * -------------------------------------------------------------------------
	 *
	 *  Additional settings for the sync process.
	 *
	 */

	'sync' => array(
		// Whether to automatically backup before sync
		'auto_backup' => true,

		// Backup directory (relative to project root)
		'backup_dir' => 'backups/',

		// Whether to run composer install after sync
		'run_composer' => true,

		// Whether to run migrations after sync
		'run_migrations' => false,
	),
);
