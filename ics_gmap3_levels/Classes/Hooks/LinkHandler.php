<?php
namespace PlanNet\IcsGmap3Levels\Hooks;

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class LinkHandler {
    /**
     * Process the link generation
     *
     * @param string $linkTxt
     * @param array $typoLinkConfiguration TypoLink Configuration array
     * @param string $linkHandlerKeyword Define the identifier that an record is given
     * @param string $linkHandlerValue Table and uid of the requested record like "2"
     * @param string $linkParams Full link params like "ics_gmap3_levels_preselect_levels:2"
     * @param ContentObjectRenderer $parentObject
     * @return string
     */
    public function main($linkTxt,
                         $typoLinkConfiguration,
                         $linkHandlerKeyword,
                         $linkHandlerValue,
                         $linkParams,
                         $parentObject) {
        unset($typoLinkConfiguration['parameter.']); // Conflit avec configuration de l'extension
        $linkConfigArray = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_icsgmap3_levels.']['linkhandler.']['ics_gmap3_levels_preselect_levels.'];

        $recordArray = array(
            'uid' => $linkHandlerValue,
        );

        $localcObj = clone $parentObject;
        $localcObj->start($recordArray, '');

        // build the full link to the record
        $generatedLink = $localcObj->typoLink($linkTxt, array_merge($linkConfigArray , $typoLinkConfiguration));

        return $generatedLink;
    }
}