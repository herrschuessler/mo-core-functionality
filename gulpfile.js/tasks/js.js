'use strict';

const gulp          = require('gulp');
const webpack       = require('webpack');
const webpackConfig = require('../webpack');

const jsTask = function() {

  return new Promise(function(resolve){

    webpack(webpackConfig, (err, stats) => {

      // The err object will not include compilation errors and those must be handled separately using stats.hasErrors()
      if (err || stats.hasErrors()) {
        console.log('Webpack', err);
      }

      console.log('[' + new Date().toLocaleTimeString() + ']', 'Webpack:', stats.toString('minimal'));

      resolve();
    });
  });
};

gulp.task('js', jsTask);
module.exports = jsTask;
