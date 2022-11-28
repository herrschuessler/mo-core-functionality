'use strict';

const config = require( './config' );
const path = require( 'path' );
const TerserPlugin = require( 'terser-webpack-plugin' );
const ESLintPlugin = require( 'eslint-webpack-plugin' );
const webpack = require( 'webpack' );

const inputPath = path.resolve( config.root.src, config.tasks.js.src );
const publicPath = path.join( config.tasks.js.dest, '/' );
const outputPath = path.resolve( config.root.build, config.tasks.js.dest );

module.exports = function() {

  let webpackConfig = {
    context: inputPath,
    target: [ 'web', 'browserslist:' + config.tasks.js.babel.browsers ],
    externals: {
      jquery: 'jQuery',
      $: 'jQuery'
    },
    resolve: {
      modules: [
        inputPath,
        "node_modules"
      ]
    },
    entry: {
      images: [ './images.js' ],
      'external-svg-sprite': [ './external-svg-sprite.js' ]
    },
    output: {
      filename: '[name].js',
      chunkFilename: '[name].js'
    },
    plugins: [
      new ESLintPlugin(),
    ],
    module: {
      rules: [
        {
          test: /\.jsx?$/,
          exclude: /node_modules\/(?!(dom7|swiper)\/).*/,
          use: [
            {
              loader: 'babel-loader',
              options: {
                presets: [
                  [ '@babel/env', {
                    targets: {
                      browsers: config.tasks.js.babel.browsers
                    },
                    useBuiltIns: 'usage',
                    corejs: 3,
                    exclude: [ 'transform-regenerator' ],
                    modules: false
                  } ]
                ],
                plugins: [ '@babel/plugin-syntax-dynamic-import' ]
              }
            }
          ]
        }
      ]
    },
    stats: 'minimal',
    optimization: {
      moduleIds: 'named',
      chunkIds: 'deterministic',
    }
  };

  webpackConfig.mode = 'production';
  webpackConfig.output.path = outputPath;
  webpackConfig.output.publicPath = publicPath;
  webpackConfig.optimization.emitOnErrors = true;
  webpackConfig.optimization.minimizer = [
    new TerserPlugin( {
      parallel: true,
      terserOptions: {
        ecma: 6,
      },
    } )
  ];


  return webpackConfig;
}();
