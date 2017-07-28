<?php
/*
 * (c) 2017 Plan.Net France <typo3@plan-net.fr>
 *
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['ics_gmap3_pi1'] = 'layout,select_key,pages';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(array(
    'LLL:EXT:ics_gmap3/Resources/Private/Language/locallang_db.xlf:tt_content.list_type_pi1',
    'ics_gmap3_pi1',
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('ics_gmap3') . 'ext_icon.gif',
), 'list_type', 'ics_gmap3');
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['ics_gmap3_pi1'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'ics_gmap3_pi1', 'FILE:EXT:ics_gmap3/Configuration/FlexForms/flexform_ds_pi1.xml');
