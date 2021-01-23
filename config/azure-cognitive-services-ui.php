<?php
return [
    'general' => [
        /*
         * Enable Routes
         * If enabled (true), the default routes will be accessible through http interface.
         * If disabled (false), the default routes will not be accessible through http interface.
         * */
        'enable_routes' => true, // true / false
    ],
    'face' => [
        'detection' => [
            /*
             * Colors to use when coloring the face rectangles in detections
             * */
            'colors' => [ // HEX codes for colors
                '#C0392B',
                '#AF7AC5',
                '#5499C7',
                '#48C9B0',
                '#F4D03F',
                '#E67E22',
                '#ECF0F1',
                '#95A5A6',
                '#34495E',
                '#641E16',
                '#D0ECE7',
                '#EBDEF0',
            ],
        ],
    ],
];
