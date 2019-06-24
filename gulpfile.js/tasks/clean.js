'use strict';

const config        = require( '../config' );
const del           = require( 'del' );
const gulp          = require( 'gulp' );
const path          = require( 'path' );

const cleanTask = function() {
	let actions = config.actions.clean;
	let promises = [];

	actions.forEach(
		function(taskName) {
			let task = config.tasks[taskName];
			if (task) {
				let glob = (typeof task.dest === 'string') ? path.join( config.root.build, task.dest ) : path.join( config.root.build, task.dest[process.env.NODE_ENV] );

				let promise = del( glob ).then(
					function (glob) {
						console.log( '[' + new Date().toLocaleTimeString() + ']', 'Cleaned ' + taskName + ' directory', glob );
					}
				);
				promises.push( promise );
			}
		}
	);

	return Promise.all( promises );
};

gulp.task( 'clean', cleanTask );
module.exports = cleanTask;
