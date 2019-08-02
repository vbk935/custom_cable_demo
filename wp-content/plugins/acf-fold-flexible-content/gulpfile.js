var gulp = require('gulp');
var plumber = require('gulp-plumber');
/*-------------------------------------------------------------------
Sync plugin folder with WP test environment every time we save

Usage:

	gulp serve

-------------------------------------------------------------------*/
var watch = require('gulp-watch');

var source = './';
var destination = '/Applications/mamp/htdocs/plugintest/wp-content/plugins/acf-fold-flexible-content/';
var destination_trunk = '/users/urbansanden/desktop/urredev/wordpress-plugins/acf-fold-flexible-content/trunk/';

gulp.task('copyfiles', function() {
  gulp.src([
  	source + '/**/*',
  	'!node_modules/**'
  	])
    .pipe(plumber({
            handleError: function (err) {
                console.log(err);
                this.emit('end');
            }
        }))
    .pipe(gulp.dest(destination));
});

gulp.task('copyfilestotrunk', function() {
  gulp.src([
    source + '/**/*',
    '!node_modules/**'
    ])
    .pipe(plumber({
            handleError: function (err) {
                console.log(err);
                this.emit('end');
            }
        }))
    .pipe(gulp.dest(destination_trunk));
});

gulp.task('serve', ['copyfiles'], function() {
    gulp.watch([source + '/**/*'], ['copyfiles']);
});