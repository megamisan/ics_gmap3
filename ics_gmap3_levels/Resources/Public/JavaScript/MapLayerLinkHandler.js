/*
 * (c) 2017 Plan.Net France <typo3@plan-net.fr>
 */

/**
 * Module: TYPO3/CMS/IcsGmap3Levels/MapLayerLinkHandler
 * Map layer link interaction
 */
define(['jquery', 'TYPO3/CMS/Recordlist/LinkBrowser'], function ($, LinkBrowser) {
    'use strict';

    /**
     *
     * @type {{currentLink: string}}
     * @exports TYPO3/CMS/IcsGmap3Levels/MapLayerLinkHandler
     */
    var MapLayerLinkHandler = {
        currentLink: ''
    };

    /**
     *
     * @param {Event} event
     */
    var toggleLevel = function (event) {
        event.preventDefault();
        var $checkBox = $('input.map-layer-leaf[value="' + $(this).data('id') + '"]');
        $checkBox.prop("checked", !$checkBox.prop("checked"));
        this.blur();
    };

    /**
     *
     * @param {Event} event
     */
    var createMyLink = function (event) {
        event.preventDefault();
        var $checkBoxes = $('input.map-layer-leaf:checked');
        if ($checkBoxes.length === 0) {
            alert($('.mapLayer-tabContent').data('saveAlert'));
            return;
        }
        var values = [];
        $checkBoxes.each(function (index, input) {
            values.push(input.value);
        });
        var val = values.join(",");
        LinkBrowser.finalizeFunction('ics_gmap3_levels_preselect_levels:' + val);
    };

    MapLayerLinkHandler.initialize = function () {
        MapLayerLinkHandler.currentLink = $('body').data('currentLink');
        $('.t3js-mapLayerLink').on('click', toggleLevel);
        $('#llinkform').on('submit', createMyLink);
    };

    $(MapLayerLinkHandler.initialize);

    return MapLayerLinkHandler;
});
