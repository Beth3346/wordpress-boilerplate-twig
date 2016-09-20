'use strict';

const gulp = require('gulp');
const del = require('del');
const sass = require('gulp-sass');
const scsslint = require('gulp-scss-lint');
const autoprefixer = require('gulp-autoprefixer');
const cleanCSS = require('gulp-clean-css');
const csslint = require('gulp-csslint');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const imagemin = require('gulp-imagemin');
const sourcemaps = require('gulp-sourcemaps');
const jshint = require('gulp-jshint');
const babel = require('gulp-babel');

let paths = {
    scss: 'assets/scss/**/*.scss',
    css: 'assets/css/*.css',
    scripts: ['assets/js/vendor/**/*.js', 'assets/js/assets/**/*.js', 'assets/js/main.js'],
    imagesRaw: ['assets/images/raw/**/*.jpg', 'assets/images/raw/**/*.png', 'assets/images/raw/**/*.gif', 'assets/images/raw/**/*.svg'],
    images: ['assets/images/*.jpg', 'assets/images/*.png', 'assets/images/*.gif', 'assets/images/*.svg']
};

gulp.task('clean:styles', function() {
    return del([
        'assets/css',
    ]);
});

gulp.task('clean:scripts', function() {
    return del([
        'assets/js/*.min.js'
    ]);
});

gulp.task('clean:images', function() {
    return del(paths.images);
});

gulp.task('clean', ['clean:styles', 'clean:scripts', 'clean:images']);

gulp.task('styles', ['scsslint', 'clean:styles'], function() {

    return gulp.src(paths.scss)
        .pipe(sass().on('error', sass.logError))
            .pipe(gulp.dest('assets/css'))
        .pipe(autoprefixer({
            browsers: ['last 5 versions'],
            cascade: false
        }))
        .pipe(gulp.dest('assets/css'))
        // .pipe(csslint({
        //     'unique-headings': false,
        //     'font-sizes': false,
        //     'box-sizing': false,
        //     'floats': false,
        //     'duplicate-background-images': false,
        //     'font-faces': false,
        //     'star-property-hack': false,
        //     'qualified-headings': false,
        //     'ids': false,
        //     'text-indent': false,
        //     'box-model': false,
        //     'adjoining-classes': false,
        //     'compatible-vendor-prefixes': false,
        //     'important': false,
        //     'unqualified-attributes': false,
        //     'fallback-colors': false,
        //     'order-alphabetical': false
        // }))
        // .pipe(csslint.formatter())
        .pipe(cleanCSS({debug: true}))
        .pipe(gulp.dest('assets/css'));
});

gulp.task('scsslint', function() {
    return gulp.src('assets/scss/partials/**/*.scss')
        .pipe(scsslint());
});

gulp.task('jshint', function() {
    return gulp.src(['assets/js/assets/**/*.js'])
        .pipe(jshint())
        .pipe(jshint.reporter('default'));
});

gulp.task('babel', ['jshint', 'clean:scripts'], function() {
    return gulp.src(paths.scripts)
        .pipe(babel({
            presets: ['es2015']
        }))
        .pipe(gulp.dest('assets/js/'));
});

gulp.task('scripts', ['babel'], function() {
    return gulp.src('assets/js/*.js')
    .pipe(sourcemaps.init())
        .pipe(concat('main.min.js'))
        .pipe(uglify())
    .pipe(sourcemaps.write())
    .pipe(gulp.dest('assets/js'));
});

gulp.task('images', ['clean:images'], function() {
    return gulp.src(paths.imagesRaw)
        .pipe(imagemin())
        .pipe(gulp.dest('assets/images'));
});

gulp.task('watch', function() {
    gulp.watch(paths.styles, ['styles']);
    gulp.watch(paths.scripts, ['scripts']);
    gulp.watch(paths.imagesRaw, ['images']);
});

gulp.task('default', ['styles', 'scripts', 'images']);