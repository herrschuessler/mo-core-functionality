'use strict';

const gulp = require( 'gulp' );
const requireDir = require( 'require-dir' );

// Require all tasks in gulpfile.js/tasks, including subfolders
requireDir( './tasks', { recurse: true } )

// Asset Task runs all assets but JS
gulp.task(
  'assets',
  gulp.parallel( 'js' )
);

// Build Task
gulp.task(
  'build',
  gulp.series(
    'clean',
    'assets'
  )
);

// Default Task =  Dev Task
gulp.task(
  'default',
  gulp.series( 'clean', 'assets', 'watch' )
);
