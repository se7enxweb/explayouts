<?php
/**
 * Converts the block links stored as ibexa-location://<id> to
 * exp-remote-location://<remote id>, in every status: published, draft and
 * archived.
 *
 * ibexa-location://<id> names a location of the reference installation, which
 * is mapped to a node of this one through [NexusNodeMap] in explayouts.ini
 * (expLayoutsDynamicCollection::remapNodeId()). The script resolves each one
 * exactly that way and stores the remote id of the node it lands on, so the
 * rendered link does not change. A remote id is used rather than a node id
 * because node ids are handed out at install time and differ between
 * installations; remote ids come with the content package.
 *
 * Run it once after upgrading explayouts, from the installation's root:
 *
 *   php extension/explayouts/bin/php/updatelinklocationreferences.php --dry-run
 *   php extension/explayouts/bin/php/updatelinklocationreferences.php
 *
 * Add -s <siteaccess> for an installation whose database is set per
 * siteaccess. Running it again changes nothing. Until it has run, the old
 * values are still read, so nothing breaks in between. A reference that
 * resolves to no node is left as it is and reported. Afterwards, clear the
 * caches that hold rendered blocks:
 *
 *   php bin/php/ezcache.php --clear-id=content,template-block --allow-root-user
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */
require_once 'autoload.php';

// The code is in extension/explayouts/classes/runnable/commands/php_updatelinklocationreferences.php (#207); this file is the entry point.
\Exponential\Command\Extension\Explayouts\Updatelinklocationreferences::main( __FILE__ );
