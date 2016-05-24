
'use strict';

const gulp = require('gulp');
const phpunit = require('gulp-phpunit');

gulp.task('test', function () {
    gulp.src('phpunit.xml')
        .pipe(phpunit('./vendor/bin/phpunit', {
            clear: true,
            notify: true
        }));
});

gulp.task('watch', function () {
    gulp.watch([
        'src/**/*.php',
        'tests/**/*.php',
        'phpunit.xml'
    ], ['test']);
});

gulp.task('default', ['test', 'watch']);

