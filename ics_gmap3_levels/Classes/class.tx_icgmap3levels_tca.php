<?php

/**
* 
*/
class tx_icsgmap3levels_tca{

	/**
	 * [getTitle description]
	 * @param  [type] $parameters   [description]
	 * @param  [type] $parentObject [description]
	 * @return [type]               [description]
	 */
	public function getTitle(&$parameters, $parentObject) {
        $record = t3lib_BEfunc::getRecord($parameters['table'], $parameters['row']['uid']);
        if($record['parent'])
        	$newTitle = $this->getLevels($record['parent'],$parameters['table']).'->';
        $newTitle .= $record['title'];
        $parameters['title'] = $newTitle;
	}

	/**
	 * [getLevels description]
	 * @param  [type] $parent [description]
	 * @param  [type] $table  [description]
	 * @return [type]         [description]
	 */
	public function getLevels($parent,$table){
		$record = t3lib_BEfunc::getRecord($table, $parent);
		return $record['title'];
	}
}


?>