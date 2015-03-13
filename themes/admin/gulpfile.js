var gulp = require('gulp');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var minifyCSS = require('gulp-minify-css');
var ngmin = require('gulp-ngmin');
var rename = require('gulp-rename');
var manifest = require('gulp-manifest');
var fs = require('fs');
var path = require('path');
var md5 = require('MD5');


gulp.task('manifest', function() {
    return gulp.src(['build/**',]).pipe(manifest({
        timestamp    : true,
        preferOnline : true,
        network      : ['http://*', 'https://*'],
        cache        : [
            'bower_components/angular-route/angular-route.min.js',
            'bower_components/angular-touch/angular-touch.min.js',
            'bower_components/angular-animate/angular-animate.min.js',
            'bower_components/angular-sanitize/angular-sanitize.min.js',
            'images/common/point_bg.png', 'images/error/404.png',
            'images/error/pad_404.png', 'images/home/discover-bg-map.png',
            'images/home/discover-bg-pattern.jpg',
            'images/home/recommend-bg.png', 'images/product/long-tag.png',
            'images/product/long-tag-phone.png',
            'images/product/ribbon-text.png', 'images/product/time.png'
        ],
        filename     : 'hitour.manifest',
        exclude      : 'hitour.manifest',
        verbose      : true


    })).pipe(gulp.dest('build'));
});

gulp.task('prepare', function() {

    var resources_original_path = 'views/resources/';
    var resources_original = fs.readdirSync(resources_original_path);
    resources_original.map(function(filename) {
            console.log('------------ start prepare ' + filename + '......');
            console.log('resource filename: ' + filename);
            var content = fs.readFileSync(resources_original_path + filename, {encoding : 'utf8'});
            var js_matched = content.match(/javascripts\/.*\.js/mg);

            if(js_matched) {
                var js_need_minify = [];
                js_matched.map(function(item) {

                    console.log('\njs matched: ' + item);
                    var js_item = item.match(/javascripts\/(.*\.js)/);
                    js_file = js_item[1];

                    if(js_file.indexOf('.min.js') === -1 && js_file.length > 0) {
                        //console.log('\njs file: ' + js_file);
                        js_need_minify.push(item);
                    }
                });

                if(js_need_minify.length > 0) {
                    console.log('length: ' + js_need_minify.length);
                    console.log('js files to be minified: ' + js_need_minify);
                    var js_min_filename = filename.replace('.php', '.min.js');
                    //console.log('\njs min filename: ' + js_min_filename);

                    gulp.src(js_need_minify).pipe(ngmin()).pipe(uglify()).pipe(concat(js_min_filename)).pipe(gulp.dest('build/javascripts/'));
                }
            }

            var css_matched = content.match(/stylesheets\/.*\.css/mg);
            if(css_matched) {
                //console.log('\ncss matched: ' + css_matched);
                var css_min_filename = filename.replace('.php', '.min.css');
                gulp.src(css_matched).pipe(minifyCSS()).pipe(concat(css_min_filename)).pipe(gulp.dest('stylesheets/'));
            }

            console.log('\n------------ end of prepare ' + filename + '-------------');
        }
    );
});

gulp.task('update_resources', ['prepare'], function() {

    var resources_path = 'views/resources.release/';

    var resources_original_path = 'views/resources/';
    var resources_original = fs.readdirSync(resources_original_path);
    resources_original.map(function(filename) {
            console.log('------------ start handling ' + filename + '......');
            console.log('resource filename: ' + filename);
            var content = fs.readFileSync(resources_original_path + filename, {encoding : 'utf8'});
            var js_matched = content.match(/javascripts\/.*\.js(\?v=.*)?\"/mg);

            var new_content = content;

            if(js_matched) {
                js_matched.map(function(item) {
                    //console.log('\njs matched: ' + item);
                    var js_item = item.match(/javascripts\/(.*\.js)(\?v=.*)?\"/);
                    js_file = js_item[1];

                    if(js_file.indexOf('.min.js') === -1 && js_file.length > 0) {
                        //console.log('\njs file: ' + js_file);

                        //  delete this line
                        var r = new RegExp('<script.*' + item + '.*<\/script>', 'mg');
                        var full_line = new_content.match(r);
                        if(full_line.length > 0) {
                            //console.log('full line: ' + full_line);
                            new_content = new_content.replace(full_line, '');
                            //console.log('new_content: ' + new_content);
                        }
                    }
                });


                var js_min_filename = filename.replace('.php', '.min.js');
                //console.log('\njs min filename: ' + js_min_filename);
                if(fs.existsSync('build/javascripts/' + js_min_filename)) {
                    //  update new_content
                    var js_content = fs.readFileSync('build/javascripts/' + js_min_filename);
                    var md5_str = '?v=' + md5(js_content);
                    new_content = new_content +
                                  '\n<script type="text/javascript" src="themes/mobile/build/javascripts/' +
                                  js_min_filename + md5_str + '"></script>';
                }
            }

            var css_matched = content.match(/stylesheets\/.*\.css/mg);
            if(css_matched) {
                //console.log('\ncss matched: ' + css_matched);
                var css_min_filename = filename.replace('.php', '.min.css');

                for(var i = 0; i < css_matched.length; i++) {
                    // remove original css file
                    var r = new RegExp('<link rel=.*' + css_matched[i] + '.*/>', 'mg');
                    var full_line = new_content.match(r);
                    if(full_line.length > 0) {
                        //console.log('full line: ' + full_line);
                        new_content = new_content.replace(full_line, '');
                        //console.log('new_content: ' + new_content);
                    }
                }

                var css_content = fs.readFileSync('stylesheets/' + css_min_filename);
                var md5_str = '?v=' + md5(css_content);
                new_content = new_content +
                              '\n<link rel="stylesheet" href="themes/mobile/stylesheets/' + css_min_filename +
                              md5_str + '" />';
            }

            var release_file = resources_path + filename;

            if(!fs.existsSync(release_file)) {
                console.log('Create ' + release_file);
                fs.writeFileSync(resources_path + filename, new_content, {encoding : 'utf8'});
            } else {
                var release_content = fs.readFileSync(release_file, {encoding : 'utf8'});
                if(release_content != new_content) {
                    console.log('Update ' + release_file);
                    fs.writeFileSync(resources_path + filename, new_content, {encoding : 'utf8'});
                } else {
                    console.log('No change.');
                }
            }

            console.log('\n------------ end of handling ' + filename + '-------------');
        }
    );
});

gulp.task('update_manifest', ['manifest'], function() {
    var content_current = fs.readFileSync('manifest.appcache');
    var content_new = fs.readFileSync('build/hitour.manifest');

    if(content_current !== content_new) {
        console.log('manifest.appcache changed.');
        //fs.renameSync( 'build/hitour.manifest', 'manifest.appcache' );
    }
});

gulp.task('default', ['update_resources']);