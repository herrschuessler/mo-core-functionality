'use strict';

const config = require( '../config' );
const gulp = require( 'gulp' );
const wpPot = require( 'gulp-wp-pot' ); // Must use version 1 to parse twig files, alternative: https://gist.github.com/luism-s/ebca42b8b8d70e81f8917f675a784060


const potTask = function() {

	return gulp.src( config.tasks.pot.src )
		.pipe( wpPot( {
			domain: config.tasks.pot.domain,
			package: config.tasks.pot.package
		} ) )
		.pipe( gulp.dest( config.tasks.pot.dest ) );
};

gulp.task( 'pot', potTask );
module.exports = potTask;
