<?php

namespace PlanNet\IcsGmap3Levels\Hooks;

use TYPO3\CMS\Backend\Form\FormEngine;
use \TYPO3\CMS\Core\ElementBrowser\ElementBrowserHookInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class BrowseLinks
 * Permet d'ajouter un nouveau type de lien dans BrowseLinks
 * Ce nouveau type de lien permet de sélectionner des couches de cartographie
 * @see TYPO3\CMS\Rtehtmlarea\BrowseLinks
 */
class BrowseLinks implements ElementBrowserHookInterface {
    /** @var \TYPO3\CMS\Rtehtmlarea\BrowseLinks */
    protected $parentObject;

    /** @var string  */
    private $tempRenderField = '';

    /**
     * @inheritDoc
     */
    public function init($parentObject, $additionalParameters)
    {
        $this->parentObject = $parentObject;

        $curUrlArray = GeneralUtility::_GP('curUrl');
        if (GeneralUtility::_GP('act') == 'ics_gmap3_levels_preselect_levels' || strpos('ics_gmap3_levels_preselect_levels', $curUrlArray['href']) >= 0) {
            // L'initialisation du champ tx_icsgmap3levels_level se fait dans init() car il est nécessaire d'inclure
            // des fichiers JS
            // Initialisation pour TreeElement
            $GLOBALS['SOBE']->doc = $this->parentObject->doc;
            /** @var FormEngine $form */
            $form = GeneralUtility::makeInstance('TYPO3\CMS\Backend\Form\FormEngine');
            $this->tempRenderField = $form->getSingleField('tt_content', 'tx_icsgmap3levels_level', array());

            // ajout de CSS spécifique pour surcharger le style de sysext/t3skin/rtehtmlarea/htmlarea.css
            $this->parentObject->doc->getPageRenderer()->addCssInlineBlock('ics_gmap3_levels_preselect_levels_treeElement',
                '.htmlarea-window-table input[type="checkbox"] {
                    margin: 0 0 0 1px;
                    height: 13px;
                    vertical-align: middle;
                }');

            $this->parentObject->doc->getPageRenderer()->addJsInlineCode('ics_gmap3_levels_preselect_levels_treeElement',
                'if (typeof TBE_EDITOR === \'undefined\') {
                    TBE_EDITOR = {};
                    TBE_EDITOR.fieldChanged = function (record,row,field,name) { return true;}
                }');
        }

        // On est dans le cas d'une modification d'un lien, il faut mettre à jour le DOM de TreeElement
        // afin que les couches soient préselectionnées
        if (strpos('ics_gmap3_levels_preselect_levels', $curUrlArray['href']) >= 0) {
            list( , $uidsLevels) = explode(':', $curUrlArray['href']);
            $uidsLevels = GeneralUtility::intExplode(',', $uidsLevels);

            $this->parentObject->doc->getPageRenderer()->addJsInlineCode('ics_gmap3_levels_preselect_levels_treeElement_prefill',
                '
                setTimeout(function() {
                    var idTree = document.getElementsByName(\'data[tt_content][][tx_icsgmap3levels_level]\')[0].id;
                    idTree = idTree.replace(\'treeinput\', \'\');
                    var tree = $(idTree),
                        uidLevelsSelected = [' . implode(',', $uidsLevels). '],
                        elements = tree.select(\'.x-tree-node-el\');
                    elements.each(function (element) {
                        var uid = parseInt(element.readAttribute(\'ext:tree-node-id\'));
                        // Utilisation de isNaN(parseInt()) pour tester si c est un entier
                        if (!isNaN(uid)) {
                            if (uidLevelsSelected.indexOf(uid) >= 0) {
                                element.select(\'input[type="checkbox"]\')[0].checked = true;
                            }
                        }
                    });
                }, 500);');
        }
    }

    /**
     * @inheritDoc
     */
    public function addAllowedItems($currentlyAllowedItems)
    {
        $currentlyAllowedItems[] = 'ics_gmap3_levels_preselect_levels';
        return $currentlyAllowedItems;
    }

    /**
     * @inheritDoc
     */
    public function modifyMenuDefinition($menuDefinition)
    {
        if (in_array('ics_gmap3_levels_preselect_levels', $this->parentObject->allowedItems)) {
            $menuDefinition['ics_gmap3_levels_preselect_levels']['isActive'] = $this->parentObject->act == 'ics_gmap3_levels_preselect_levels';
            $menuDefinition['ics_gmap3_levels_preselect_levels']['label'] = 'Cartographie';
            $menuDefinition['ics_gmap3_levels_preselect_levels']['url'] = '#';
            $menuDefinition['ics_gmap3_levels_preselect_levels']['addParams'] = 'onclick="jumpToUrl(' . GeneralUtility::quoteJSvalue('?act=ics_gmap3_levels_preselect_levels&mode=' . $this->parentObject->mode . '&bparams=' . $this->parentObject->bparams) . ');return false;"';
        }
        return $menuDefinition;
    }

    /**
     * @inheritDoc
     */
    public function getTab($linkSelectorAction)
    {
        $content = '
            <tr>
                <td colspan="3">' . $this->tempRenderField .'</td>
                <td align="middle"><input type="submit" value="Attribuer le lien" onclick="browse_links_setHref(\'ics_gmap3_levels_preselect_levels:\' + document.getElementsByName(\'data[tt_content][][tx_icsgmap3levels_level]\')[0].value);return link_current();" /></td>
            </tr>';

        return $this->parentObject->addAttributesForm($content);
    }

    /**
     * @inheritDoc
     */
    public function parseCurrentUrl($href, $siteUrl, $info)
    {
        list($type, $uids) = explode(":", $href);
        if ($type == 'ics_gmap3_levels_preselect_levels') {
            $info['act'] = 'ics_gmap3_levels_preselect_levels';
            $info['value'] = $uids;
        }
        return $info;
    }

}