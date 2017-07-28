<?php

namespace PlanNet\IcsGmap3Levels;

use TYPO3\CMS\Backend\Utility\BackendUtility;

/**
 *
 */
class Tca {

    /**
     * @param array $parameters
     * @param object $parentObject
     */
    public function getTitle(&$parameters, $parentObject) {
        $record = BackendUtility::getRecord($parameters['table'], $parameters['row']['uid']);
        $newTitle = $record['title'];
        if ($record['parent']) {
            $newTitle = $this->getLevels($record['parent'], $parameters['table']) . '->' . $newTitle;
        }
        $parameters['title'] = $newTitle;
    }

    /**
     * @param string $parent
     * @param int $table
     * @return string
     */
    public function getLevels($parent, $table) {
        $record = BackendUtility::getRecord($table, $parent);
        return $record['title'];
    }
}
