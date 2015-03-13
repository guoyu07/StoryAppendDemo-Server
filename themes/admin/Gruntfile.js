module.exports = function(grunt) {
    grunt.initConfig({
        less  : {
            development : {
                files   : {
                    'stylesheets/_custom_bootstrap.css' : 'less/_custom_bootstrap.less',
                    'stylesheets/_custom_flatly.css'    : 'less/_custom_flatly.less',
                    'stylesheets/styles.css'            : 'less/styles.less'
                },
                options : {
                    relativeUrls : true
                }
            }
        },
        watch : {
            all : {
                files : ['less/**/*', 'views/admin/**/less/*', 'views/modules/**/*'],
                tasks : ['less']
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-watch');
};
