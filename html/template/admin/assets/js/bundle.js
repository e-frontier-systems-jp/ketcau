const $ = require('jquery');
global.$ = global.jQuery = $;

require('ace-builds/src-min-noconflict/ace');
require('ace-builds/src-min-noconflict/ext-language_tools')
require('ace-builds/src-min-noconflict/mode-twig')

require('jquery-ui/themes/base/all.css');
require('jquery-ui/ui/core');
require('jquery-ui/ui/position');
require('jquery-ui/ui/widget');
require('jquery-ui/ui/widgets/mouse');
require('jquery-ui/ui/widgets/resizable')
require('jquery-ui/ui/widgets/sortable')
require('jquery-ui/ui/widgets/tooltip')

require('ladda/dist/ladda-themeless.min.css');
global.Ladda = require('ladda');

const bootstrap = require('bootstrap');
global.bootstrap = bootstrap;


import '../scss/app.scss';