'use strict';

const config      = require('./config');
const path        = require('path');
const SizePlugin  = require('size-plugin');
const webpack     = require('webpack');

const inputPath  = path.resolve(config.root.src, config.tasks.js.src);
const publicPath = path.join(config.tasks.js.dest, '/');
const outputPath = path.resolve(config.root.build, config.tasks.js.dest);

module.exports = function() {

  let webpackConfig = {
    context: inputPath,
    target: 'web',
    externals: {
      jquery: 'jQuery',
      $: 'jQuery'
    },
    resolve: {
      modules: [
      inputPath,
      "node_modules"
      ],
      alias: {
        'vue$': 'vue/dist/vue.esm.js'
      }
    },
    entry: {
      images: ['./images.js']
    },
    output: {
      filename: '[name].js',
      chunkFilename: '[name].js'
    },
    plugins: [
    new SizePlugin()
    ],
    module: {
      rules: [
      {
        enforce: "pre",
        test: /\.jsx?$/,
        exclude: /node_modules\/(?!(dom7|swiper)\/).*/,
        loader: "eslint-loader"
      },
      {
        test: /\.jsx?$/,
        loader: 'babel-loader',
        exclude: /node_modules\/(?!(dom7|swiper)\/).*/,
        options: {
          presets: [
          ['@babel/env', {
            targets: {
              browsers: config.tasks.js.babel.browsers
            },
            useBuiltIns: 'usage',
            corejs: 3,
            exclude: ['transform-regenerator'],
            modules: false
          }]
          ],
          plugins: ['@babel/plugin-syntax-dynamic-import']
        }
      }
      ]
    },
    stats: 'minimal'
  };

  webpackConfig.mode = 'development';
  webpackConfig.output.path = outputPath;
  webpackConfig.output.publicPath = publicPath;
  webpackConfig.plugins.push(
    new webpack.optimize.OccurrenceOrderPlugin(),
    new webpack.NoEmitOnErrorsPlugin()
    );
  webpackConfig.module.rules.push({
    test: /\.jsx?$/,
    loader: 'strip-loader?strip[]=console.log,strip[]=console.group,strip[]=console.groupEnd'
  });


  return webpackConfig;
}();
