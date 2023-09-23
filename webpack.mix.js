const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js');
mix.js('resources/js/cart.js', 'public/js');
mix.js('resources/js/admin.js', 'public/js');
mix.js('resources/js/dashboard.js', 'public/js');
mix.js('resources/js/store.js', 'public/js');

mix.sass('resources/css/app.scss', 'public/css');
mix.sass('resources/css/css.scss', 'public/css');
mix.sass('resources/css/admin.scss', 'public/css');
mix.sass('resources/css/admin_login.scss', 'public/css')
mix.sass('resources/css/store_login.scss', 'public/css')
