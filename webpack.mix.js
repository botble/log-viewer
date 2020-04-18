let mix = require('laravel-mix');

const publicPath = 'public/vendor/core/plugins/log-viewer';
const resourcePath = './platform/plugins/log-viewer';

mix
    .sass(resourcePath + '/resources/assets/sass/log-viewer.scss', publicPath + '/css')
    .copy(publicPath + '/css/log-viewer.css', resourcePath + '/public/css');