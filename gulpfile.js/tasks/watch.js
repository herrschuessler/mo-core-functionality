'use strict';

const config        = require( '../config' );
const gulp          = require( 'gulp' );
const path          = require( 'path' );
const watch         = require( 'gulp-watch' );

const watchTask = function(done) {
	let actions = config.actions.watch;
	actions.forEach(
		function(taskName) {
			let task = config.tasks[taskName];
			if (task) {
				let glob = path.join( config.root.src, task.src, (task.extensions.length > 1 ? '**/*.{' + task.extensions.join( ',' ) + '}' : '**/*.' + task.extensions[0]) );
				watch(
					glob,
					function() {
						require( './' + taskName )( done );
						console.log( '[' + new Date().toLocaleTimeString() + ']', 'Change detected, running task ' + taskName )
					}
				);
			}
		}
	)
	global.isWatching = true;
	done();
};

gulp.task( 'watch', watchTask );
