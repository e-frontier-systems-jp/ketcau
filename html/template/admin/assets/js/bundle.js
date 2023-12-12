const $ = require('jquery');
global.$ = global.jQuery = $;

const bootstrap = require('bootstrap');
global.bootstrap = bootstrap;

require('jquery-ui/themes/base/all.css');
require('jquery-ui/ui/core');
require('jquery-ui/ui/position');
require('jquery-ui/ui/widget');
require('jquery-ui/ui/widgets/mouse');
require('jquery-ui/ui/widgets/resizable')
require('jquery-ui/ui/widgets/sortable')
require('jquery-ui/ui/widgets/tooltip')

import '../scss/app.scss';