let mix = require('laravel-mix');
const path = require('path');
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

mix.js('resources/js/app.js', 'public/js')
    .js('resources/js/firebase-messaging-sw.js', 'public/')
    // .postCss('resources/css/app.css', 'public/css' ,[ ])
    .postCss('resources/css/all.css', 'public/css', [])
    .postCss('resources/css/main.css', 'public/css', [])

    .webpackConfig({
        output: {chunkFilename: 'js/[name].js?id=[chunkhash]'},
        resolve: {
            alias: {
                //vue$: 'vue/dist/vue.esm.js',
                '@': path.resolve('resources/js'),
            },
        },
    })
    .version()
    .vue();

