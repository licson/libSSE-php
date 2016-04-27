
'use strict';

const gulp = require('gulp');
const watch = require('gulp-watch');
const phpunit = require('gulp-phpunit');

gulp.task('test', function () {
    gulp.src('phpunit.xml')
        .pipe(phpunit('./vendor/bin/phpunit', {
            clear: true,
            notify: true
        }));
});

gulp.task('watch', function () {
    watch([
        'src/**/*.php',
        'tests/**/*.php',
        'phpunit.xml'
    ], function () {
        gulp.run('test');
    });
});
