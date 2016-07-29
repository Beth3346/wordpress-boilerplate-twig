module.exports = function(grunt) {

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        appFolder: '<%= pkg.name %>',

        clean: {
            stylesheets: {
                nonull: false,
                src: ['assets/css/*.css', 'assets/css/*.css.map']
            },

            scripts: {
                nonull: false,
                src: ['assets/js/assets/js/main.<%= pkg.version %>.js', 'assets/js/main.<%= pkg.version %>.min.js']
            },

            // images: {
            //     nonull: false,
            //     src: ['assets/images']
            // }
        },

        concat: {
            options: {
                // define a string to put between each file in the concatenated output
                separator: ';'
            },

            dist: {
                // the files to concatenate
                src: ['assets/js/vendor/**/*.js', 'assets/js/assets/**/*.js', 'assets/js/main.js'],
                // the location of the resulting JS file
                dest: 'assets/js/main.<%= pkg.version %>.js'
            }
        },

        jshint: {
            files: ['assets/js/assets/**/*js', 'assets/js/main.js'],
            options: {
                maxerr: 10,
                unused: true,
                eqnull: true,
                eqeqeq: true,
                jquery: true
            }
        },

        uglify: {
            my_target: {
                options: {
                    mangle: false
                },

                files: {
                    'assets/js/main.<%= pkg.version %>.min.js': ['assets/js/main.<%= pkg.version %>.js']
                }
            }
        },

        imagemin: {
            dist: {
                options: {
                    optimizationLevel: 7
                },
                files: [{
                    expand: true,
                    cwd: 'assets/images',
                    src: '**/*.{png,jpg,jpeg}',
                    dest: 'assets/images'
                }]
            }
        },

        sass: {
            dev: {
                options: {
                    style: 'expanded'
                },
                files: [{
                    expand: true,
                    cwd: 'assets/sass',
                    src: ['**/*.scss'],
                    dest: 'assets/css',
                    ext: '.css'
                }]
            },
            dist: {
                options: {
                    style: 'compact'
                },
                files: [{
                    expand: true,
                    cwd: 'assets/sass',
                    src: ['**/*.scss'],
                    dest: 'assets/css',
                    ext: '.css'
                }]
            }
        },

        scsslint: {
            allFiles: [
                'assets/sass/partials/**/*.scss',
            ],
            options: {
                bundleExec: false,
                colorizeOutput: true,
                config: '.scss-lint.yml',
                reporterOutput: null
            }
        },

        autoprefixer: {
            options: {
                browsers: ['last 8 versions']
            },
            build: {
                expand: true,
                flatten: true,
                src: 'assets/css/*.css', // -> src/css/file1.css, src/css/file2.css
                dest: 'assets/css/' // -> dest/css/file1.css, dest/css/file2.css
            }
        },

        csslint: {
            strict: {
                options: {
                    "unique-headings": false,
                    "font-sizes": false,
                    "box-sizing": false,
                    "floats": false,
                    "duplicate-background-images": false,
                    "font-faces": false,
                    "star-property-hack": false,
                    "qualified-headings": false,
                    "ids": false,
                    "text-indent": false,
                    "box-model": false,
                    "adjoining-classes": false,
                    "compatible-vendor-prefixes": false,
                    "important": false,
                    "unqualified-attributes": false,
                    "fallback-colors": false
                },
                src: ['*.css']
            }
        },

        svgmin: {
            options: {
                plugins: [
                    {removeViewBox: false},               // don't remove the viewbox atribute from the SVG
                    {removeUselessStrokeAndFill: false},  // don't remove Useless Strokes and Fills
                    {removeEmptyAttrs: false}             // don't remove Empty Attributes from the SVG
                ]
            },
            dist: {
                files: [{
                    expand: true,        // Enable dynamic expansion.
                    cwd: 'assets/images',  // Src matches are relative to this path.
                    src: ['*.svg'],     // Actual pattern(s) to match.
                    dest: 'assets/images',  // Destination path prefix.
                    ext: '.svg',         // Dest filepaths will have this extension.
                }]
            }
        },

        // babel: {
        //     options: {
        //         sourceMap: true,
        //         presets: ['babel-preset-es2015']
        //     },
        //     dist: {
        //         files: {
        //             ''
        //         }
        //     },
        // },

        watch: {
            images: {
                files: ['assets/images/**/*.{png,jpg,jpeg}'],
                tasks: ['imagemin'],
            },
            svg: {
                files: ['assets/images/**/*.{svg}'],
                tasks: ['svgmin'],
            },
            sass: {
                // We watch and compile sass files as normal but don't live reload here
                files: ['assets/sass/**/*.scss'],
                tasks: ['sass:dev', 'scsslint', 'csslint'],
            },
            scripts: {
                // We watch and compile sass files as normal but don't live reload here
                files: ['assets/js/**/*.js'],
                tasks: ['concat', 'jshint'],
            }
        }

    });

    // Load the plugin that provides the "uglify" task.
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-imagemin');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-csslint');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-autoprefixer');
    grunt.loadNpmTasks('grunt-scss-lint');
    grunt.loadNpmTasks('grunt-browser-sync');
    grunt.loadNpmTasks('grunt-svgmin');
    grunt.loadNpmTasks('grunt-babel');

    // Default task(s).
    grunt.registerTask('default', [
        'clean',
        'imagemin',
        'svgmin',
        'concat',
        'jshint',
        'uglify',
        'sass:dev',
        'autoprefixer',
        'scsslint',
        'csslint',
        'watch'
    ]);
    grunt.registerTask('build', [
        'clean',
        'imagemin',
        'svgmin',
        'concat',
        'jshint',
        'uglify',
        'sass:dist',
        'autoprefixer',
        'scsslint',
        'csslint'
    ]);
};