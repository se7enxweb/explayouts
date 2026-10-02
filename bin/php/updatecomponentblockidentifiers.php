<?php
/**
 * Renames the content component blocks stored under their old identifiers,
 * ibexa_component_<type>, to exp_component_<type> (hero, features, about,
 * logos, quote, lead), in every status: published, draft and archived.
 *
 * Run it once after upgrading explayouts, from the installation's root:
 *
 *   php extension/explayouts/bin/php/updatecomponentblockidentifiers.php --dry-run
 *   php extension/explayouts/bin/php/updatecomponentblockidentifiers.php
 *
 * Add -s <siteaccess> for an installation whose database is set per
 * siteaccess. Running it again changes nothing. Until it has run, the old
 * names still find their definitions, so nothing breaks in between.
 * Afterwards, clear the caches that hold rendered blocks (the layout resolver
 * cache holds only layout ids and can stay):
 *
 *   php bin/php/ezcache.php --clear-id=content,template-block --allow-root-user
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */
require_once 'autoload.php';

// The code is in extension/explayouts/classes/runnable/commands/php_updatecomponentblockidentifiers.php (#207); this file is the entry point.
\Exponential\Command\Extension\Explayouts\Updatecomponentblockidentifiers::main( __FILE__ );
