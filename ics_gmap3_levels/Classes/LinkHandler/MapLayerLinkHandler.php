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

namespace PlanNet\IcsGmap3Levels\LinkHandler;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\View\TemplateView;
use TYPO3\CMS\Lang\LanguageService;
use TYPO3\CMS\Recordlist\Controller\AbstractLinkBrowserController;
use TYPO3\CMS\Recordlist\LinkHandler\LinkHandlerInterface;

/**
 * MapLayerLinkHandler
 *
 * @author Pierrick Caillon <pierrick.caillon@plan-net.fr>
 */
class MapLayerLinkHandler implements LinkHandlerInterface, SingletonInterface {

    /**
     * Available additional link attributes
     * @var string[]
     */
    protected $linkAttributes = ['target', 'title', 'class'];
    /** @var IconFactory */
    protected $iconFactory;
    /** @var AbstractLinkBrowserController */
    protected $linkBrowser;
    /** @var array */
    protected $linkParts;

    /**
     * @return array
     */
    public function getLinkAttributes() {
        return $this->linkAttributes;
    }

    /**
     * @param string[] $fieldDefinitions Array of link attribute field definitions
     * @return string[]
     */
    public function modifyLinkAttributes(array $fieldDefinitions) {
        return $fieldDefinitions;
    }

    /**
     * Initialize the handler
     *
     * @param AbstractLinkBrowserController $linkBrowser
     * @param string $identifier
     * @param array $configuration Page TSconfig
     *
     * @return void
     */
    public function initialize(AbstractLinkBrowserController $linkBrowser, $identifier, array $configuration) {
        $this->linkBrowser = $linkBrowser;
        $this->iconFactory = GeneralUtility::makeInstance(IconFactory::class);
    }

    /**
     * Checks if this is the handler for the given link
     *
     * The handler may store this information locally for later usage.
     *
     * @param array $linkParts Link parts as returned from TypoLinkCodecService
     *
     * @return bool
     */
    public function canHandleLink(array $linkParts) {
        if (!$linkParts['url']) {
            return FALSE;
        }
        $this->linkParts = $linkParts;
        $match = preg_match('/^ics_gmap3_levels_preselect_levels:/', $linkParts['url']) !== 0;
        if ($match) {
            $this->linkParts['layers'] = GeneralUtility::intExplode(',', substr($linkParts['url'], 34), TRUE);
        }
        return $match;
    }

    /**
     * Format the current link for HTML output
     *
     * @return string
     */
    public function formatCurrentUrl() {
        $identifiers = $this->linkParts['layers'];
        $labels = [];
        foreach ($identifiers as $identifier) {
            $levelRow = BackendUtility::getRecord('tx_icsgmap3levels_levels', $identifier);
            if ($levelRow) {
                $labels[] = $levelRow['title'];
            }
        }
        return $GLOBALS['LANG']->sL('LLL:EXT:ics_gmap3_levels/Resources/Private/Language/locallang_browse_links.xlf:layers') .
            ' ' . implode(', ', $labels);
    }

    /**
     * Render the link handler
     *
     * @param ServerRequestInterface $request
     *
     * @return string
     */
    public function render(ServerRequestInterface $request) {
        GeneralUtility::makeInstance(PageRenderer::class)->loadRequireJsModule('TYPO3/CMS/IcsGmap3Levels/MapLayerLinkHandler');
        $levels = $this->buildLevels();
        $assign = [
            'levels' => $levels,
            'new'    => !isset($this->linkParts['layers']),
        ];
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $resourcePath = GeneralUtility::getFileAbsFileName('EXT:ics_gmap3_levels/Resources/Private/');
        $view = $objectManager->get(TemplateView::class);
        $request = $objectManager->get(Request::class);
        $request->setFormat('html');
        $request->setControllerExtensionName('IcsGmap3Levels');
        $request->setControllerVendorName('PlanNet');
        $request->setControllerName('Base');
        $request->setControllerActionName('index');
        $controllerContext = $objectManager->get(ControllerContext::class);
        $controllerContext->setRequest($request);
        $view->setControllerContext($controllerContext);
        $view->setLayoutRootPath($resourcePath . 'Layouts/');
        $view->setPartialRootPath($resourcePath . 'Partials/');
        $view->setTemplatePathAndFilename($resourcePath . 'Templates/MapLayerLinkHandler.html');
        $view->assignMultiple($assign);
        return $view->render();
    }

    /**
     * Return TRUE if the handler supports to update a link.
     *
     * This is useful for file or page links, when only attributes are changed.
     *
     * @return bool
     */
    public function isUpdateSupported() {
        return TRUE;
    }

    /**
     * @return string[] Array of body-tag attributes
     */
    public function getBodyTagAttributes() {
        if (empty($this->linkParts)) {
            return [];
        }
        return [
            'data-current-link' => $this->linkParts['url'],
            'data-identifiers'  => json_encode($this->linkParts['layers']),
        ];
    }

    /**
     * @param array $linkHandlers
     * @param array $currentLinkParts
     * @return array
     */
    public function modifyLinkHandlers($linkHandlers, $currentLinkParts) {
        $linkHandlers['mapLayer.']['displayAfter'] = $linkHandlers['file.']['displayAfter'];
        $linkHandlers['file.']['displayAfter'] = 'mapLayer';
        return $linkHandlers;
    }

    /**
     * @param array $allowedTabs
     * @param array $currentLinkParts
     * @return array
     */
    public function modifyAllowedItems($allowedTabs, $currentLinkParts) {
        if (isset($currentLinkParts['url']) && preg_match('/^ics_gmap3_levels_preselect_levels:/', $currentLinkParts['url'])) {
            return ['mapLayer'];
        }
        if (in_array('page', GeneralUtility::trimExplode(',', $this->linkBrowser->getParameters()['params']['blindLinkOptions']))) {
            $allowedTabs = array_diff($allowedTabs, ['mapLayer']);
        }
        return $allowedTabs;
    }

    /**
     * @return LanguageService
     */
    protected function getLanguageService() {
        /** @var LanguageService $lang */
        $lang = $GLOBALS['LANG'];
        return $lang;
    }

    /**
     * @return array
     */
    protected function buildLevels() {
        $levelsTemporary = [];
        $levelsShortcuts = [];
        $levels = [];
        $levelRows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
            'uid, sorting, title, parent', 'tx_icsgmap3levels_levels',
            '1' . BackendUtility::BEenableFields('tx_icsgmap3levels_levels') .
            BackendUtility::deleteClause('tx_icsgmap3levels_levels')
        );
        foreach ($levelRows as $levelRow) {
            $level = new \stdClass();
            $level->uid = intval($levelRow['uid']);
            $level->sorting = intval($levelRow['sorting']);
            $level->title = $levelRow['title'];
            $level->selected = isset($this->linkParts['layers']) && in_array($level->uid, $this->linkParts['layers']);
            $level->children = isset($levelsTemporary[$level->uid]) ? $levelsTemporary[$level->uid] : [];
            unset($levelsTemporary[$level->uid]);
            $parent = intval($levelRow['parent']);
            if ($parent === 0) {
                $levels[] = $level;
            } else {
                if (isset($levelsShortcuts[$parent])) {
                    $levelsShortcuts[$parent]->children[] = $level;
                } else {
                    $levelsTemporary[$parent][] = $level;
                }
            }
            $levelsShortcuts[$level->uid] = $level;
        }
        foreach ($levelsShortcuts as $levelsShortcut) {
            usort($levelsShortcut->children, function ($c1, $c2) {
                return $c1->sorting - $c2->sorting;
            });
        }
        usort($levels, function ($c1, $c2) {
            return $c1->sorting - $c2->sorting;
        });
        return $levels;
    }
}
