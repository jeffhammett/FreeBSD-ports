<?php
/*
 * squid_antivirus_status.widget.php
 *
 * part of pfSense (https://www.pfsense.org)
 * Copyright (c) 2015 Rubicon Communications, LLC (Netgate)
 * Copyright (C) 2010 Serg Dvoriancev <dv_serg@mail.ru>
 * All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

require_once("guiconfig.inc");
require_once("pfsense-utils.inc");
require_once("functions.inc");
require_once("pkg-utils.inc");
if (file_exists("/usr/local/pkg/squid.inc")) {
	require_once("/usr/local/pkg/squid.inc");
} else {
	echo "No squid.inc found. You must have Squid3 package installed to use this widget.";
}

define('PATH_CLAMDB', '/var/db/clamav');
define('PATH_SQUID', SQUID_BASE . '/bin/squid');
define('PATH_AVLOG', '/var/log/c-icap/virus.log');
global $clamd_path, $cicap_cfg_path, $img;
$clamd_path = SQUID_BASE . "/sbin/clamd";
$cicap_cfg_path = SQUID_LOCALBASE . "/bin/c-icap-config";
$img = array();
$img['up'] = "<img src='data:image/gif;base64,R0lGODlhCwALAIABACPcMP///yH+FUNyZWF0ZWQgd2l0aCBUaGUgR0lNUAAh+QQBCgABACwAAAAACwALAAACFYwNpwi50eKK9NA722Puyf15GjgaBQA7' title='Service running' alt='' />";
$img['down'] = "<img src='data:image/gif;base64,R0lGODlhCwALAIABANwjI////yH+FUNyZWF0ZWQgd2l0aCBUaGUgR0lNUAAh+QQBCgABACwAAAAACwALAAACFowDeYvKlsCD7sXZ5Iq89kpdFshoRwEAOw==' title='Service not running' alt='' />";

function squid_avdb_info($filename) {
	$stl = "style='padding-top: 0px; padding-bottom: 0px; padding-left: 4px; padding-right: 4px; border-left: 1px solid #999999;'";
	$r = '';
	$path = PATH_CLAMDB . "/{$filename}";
	if (file_exists($path)) {
		$handle = '';
		if ($handle = fopen($path, "r")) {
			$s = fread($handle, 1024);
			$s = explode(':', $s);
			# datetime
			$dt = explode(" ", $s[1]);
			$s[1] = strftime("%Y.%m.%d", strtotime("{$dt[0]} {$dt[1]} {$dt[2]}"));
			if ($s[0] == 'ClamAV-VDB') {
				$r .= "<tr class='listr'><td>{$filename}</td><td {$stl}>{$s[1]}</td><td {$stl}>{$s[2]}</td><td $stl>{$s[7]}</td></tr>";
			}
			fclose($handle);
		}
		return $r;
	}
}

function squid_antivirus_bases_info() {
	$db = '<table width="100%" border="0" cellspacing="0" cellpadding="1"><tbody>';
	$db .= '<tr class="vncellt" ><td>Database</td><td>Date</td><td>Version</td><td>Builder</td></tr>';
	$avdbs = array("daily.cvd", "daily.cld", "bytecode.cvd", "bytecode.cld", "main.cvd", "main.cld", "safebrowsing.cvd", "safebrowsing.cld");
	foreach ($avdbs as $avdb) {
		$db .= squid_avdb_info($avdb);
	}
	$db .= '</tbody></table>';
	return $db;
}

function squid_clamav_version() {
	global $clamd_path, $cicap_cfg_path, $img;
	if (is_executable($clamd_path)) {
		$s = (is_service_running("clamd") ? $img['up'] : $img['down']);
		$version = preg_split("@/@", shell_exec("{$clamd_path} -V"));
		$s .= "&nbsp;&nbsp;{$version[0]}";
	} else {
		$s .= "&nbsp;&nbsp;ClamAV: N/A";
	}
	if (is_executable($cicap_cfg_path)) {
		$s .= "&nbsp;&nbsp;";
		$s .= (is_service_running("c-icap") ? $img['up'] : $img['down']);
		$s .= "&nbsp;&nbsp;C-ICAP " . shell_exec("{$cicap_cfg_path} --version");
	} else {
		$s .= "&nbsp;&nbsp;C-ICAP: N/A";
	}
	if (file_exists("/usr/local/www/squid_clwarn.php")) {
		preg_match("@(VERSION.*).(\d{1}).(\d{2})@", file_get_contents("/usr/local/www/squid_clwarn.php"), $squidclamav_version);
		$s .= "+&nbsp;&nbsp;SquidClamav " . str_replace("'", "", strstr($squidclamav_version[0], "'"));
	} else {
		$s .= "+&nbsp;&nbsp;SquidClamav: N/A";
	}
	return $s;
}

function squid_avupdate_status() {
	global $clamd_path;
	$s = "N/A";
	if (is_executable($clamd_path)) {
		$lastupd = preg_split("@/@", shell_exec("{$clamd_path} -V"));
		$s = $lastupd[2];
	}
	return $s;
}

function squid_antivirus_statistics() {
	$s = "Unknown (no log exists)";
	if (file_exists(PATH_AVLOG)) {
		$log = file_get_contents(PATH_AVLOG);
		$count = substr_count(strtolower($log), "virus found");
		$s = "Found {$count} virus(es) total.";
	}
	return $s;
}

?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tbody>
		<tr>
			<td class="vncellt">Squid Version</td>
			<td class="listr" width="75%">
			<?php
				$updown = (is_service_running("squid") ? $img['up'] : $img['down']);
				pkg_exec("query '%v' squid", $version, $err);
				echo "{$updown}&nbsp;&nbsp;${version}";
			?>
			</td>
		</tr>
		<tr>
			<td class="vncellt">Antivirus Scanner</td>
			<td class="listr" width="75%">
				<?php echo squid_clamav_version(); ?>
			</td>
		</tr>
		<tr>
			<td class="vncellt">Antivirus Bases</td>
			<td class="listr" width="75%">
				<?php echo squid_antivirus_bases_info(); ?>
			</td>
		</tr>
		<tr>
			<td class="vncellt">Last Update</td>
			<td class="listr" width="75%">
				<?php echo squid_avupdate_status(); ?>
			</td>
		</tr>
		<tr>
			<td class="vncellt">Statistics</td>
			<td class="listr" width="75%">
				<?php echo squid_antivirus_statistics(); ?>
			</td>
		</tr>
	</tbody>
</table>
