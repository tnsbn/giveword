let mix = require('laravel-mix');
let fs = require('fs');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.copy('resources/images/', 'public/images/')

mix.js('resources/js/app.js', 'public/js').vue()
   .copy('resources/js/bootstrap.bundle.js', 'public/js')
   .copy('resources/js/jquery.js', 'public/js')
   .copy('resources/sass/bootstrap.css', 'public/css')
   .sass('resources/sass/app.scss', 'public/css');

/*
 |--------------------------------------------------------------------------
 | Mix standalone style *.sass files into separated files
 |--------------------------------------------------------------------------
 | Files put at folder 'resources/assets/sass/alone/'
 | will be compiled into separated files.
 |
 */
let standaloneScssPath = 'resources/sass/alone/';
let publicScssPath = 'public/css/alone';
let scssFiles = fs.readdirSync(standaloneScssPath);
for (let i = 0; i < scssFiles.length; i++) {
  if (scssFiles[i].indexOf('.scss') > 0) {
    mix.sass(standaloneScssPath + scssFiles[i], publicScssPath);
  }
}

/*
 |--------------------------------------------------------------------------
 | Mix standalone javascript *.js files into separated files
 |--------------------------------------------------------------------------
 | Files put at folder 'resources/assets/js/alone/'
 | will be compiled into separated files.
 |
 */
let standaloneJsPath = 'resources/js/alone/';
let publicJsPath = 'public/js/alone';
let jsFiles = fs.readdirSync(standaloneJsPath);
for (let i = 0; i < jsFiles.length; i++) {
  if (jsFiles[i].indexOf('.js') > 0) {
    mix.js(standaloneJsPath + jsFiles[i], publicJsPath);
  }
}

mix.copy('web.yaml', 'public/')
mix.copy('swagger.json', 'public/')

/*
 |--------------------------------------------------------------------------
 | false: Don't allow Mix to copy file and convert cascading url to public folder
 | true: Mix will copy file and convert to "../images/banner/banner_1455_245.jpg" to "public/banner_1455_245.jpg?23s192837h891787
 |--------------------------------------------------------------------------
 |
 */
mix.options({
  processCssUrls: true
});
