/* Webpack Configuration
===================================================================================================================== */

// Load Core Modules:

const path = require('path');
const webpack = require('webpack');
const autoprefixer = require('autoprefixer');

// Load Plugin Modules:

const ExtractTextPlugin = require('extract-text-webpack-plugin');
const CleanWebpackPlugin = require('clean-webpack-plugin');

// Configure Paths:

const PATHS = {
  MODULE: {
    SRC: path.resolve(__dirname, 'client/src'),
    DIST: path.resolve(__dirname, 'client/dist'),
    BUNDLES: path.resolve(__dirname, 'client/src/bundles'),
    PUBLIC: '/resources/silverware/validator/client/dist/',
  },
  MODULES: path.resolve(__dirname, 'node_modules')
};

// Configure Style Loader:

const style = (env, loaders) => {
  return (env === 'production') ? ExtractTextPlugin.extract({
    fallback: 'style-loader',
    use: loaders
  }) : [{ loader: 'style-loader' }].concat(loaders);
};

// Configure Rules:

const rules = (env) => {
  return [
    {
      test: /\.js$/,
      use: [
        {
          loader: 'babel-loader'
        }
      ],
      exclude: [ PATHS.MODULES ]
    },
    {
      test: /\.css$/,
      use: style(env, [
        {
          loader: 'css-loader'
        },
        {
          loader: 'postcss-loader',
          options: {
            plugins: [ autoprefixer ] // see "browserslist" in package.json
          }
        }
      ])
    },
    {
      test: /\.scss$/,
      use: style(env, [
        {
          loader: 'css-loader'
        },
        {
          loader: 'postcss-loader',
          options: {
            plugins: [ autoprefixer ] // see "browserslist" in package.json
          }
        },
        {
          loader: 'sass-loader',
          options: {
            includePaths: [
              path.resolve(process.env.PWD, '../') // allows resolving of framework paths in symlinked modules
            ]
          }
        }
      ])
    },
    {
      test: /\.(gif|jpg|png)$/,
      use: [
        {
          loader: 'url-loader',
          options: {
            name: 'images/[name].[ext]',
            limit: 10000
          }
        }
      ]
    }
  ];
};

// Configure Devtool:

const devtool = (env) => {
  return (env === 'production') ? false : 'source-map';
};

// Configure Plugins:

const plugins = (env, src, dist) => {
  
  // Define Common Plugins:
  
  var common = [
    new webpack.ProvidePlugin({
      $: 'jquery',
      jQuery: 'jquery',
      moment: 'moment',
      'window.Parsley': 'parsleyjs' // without this, the production build errored! :|
    })
  ];
  
  // Answer Common + Environment-Specific Plugins:
  
  return common.concat((env === 'production') ? [
    new CleanWebpackPlugin([ dist ], {
      verbose: true
    }),
    new ExtractTextPlugin({
      filename: 'styles/[name].css',
      allChunks: true
    }),
    new webpack.optimize.UglifyJsPlugin({
      output: {
        beautify: false,
        comments: false,
        semicolons: false
      },
      compress: {
        unused: false,
        warnings: false
      }
    })
  ] : [
    
  ]);
  
};

// Define Configuration:

const config = (env) => {
  return [
    {
      entry: {
        'parsley': path.resolve(PATHS.MODULE.BUNDLES, 'parsley.js')
      },
      output: {
        path: PATHS.MODULE.DIST,
        filename: 'js/[name].js',
        publicPath: PATHS.MODULE.PUBLIC
      },
      module: {
        rules: rules(env)
      },
      devtool: devtool(env),
      plugins: plugins(env, PATHS.MODULE.SRC, PATHS.MODULE.DIST),
      resolve: {
        alias: {
          'moment$': path.resolve(PATHS.MODULES, 'moment-mini')  // moment-mini excludes locales, MUCH smaller build!
        },
        modules: [
          PATHS.MODULE.SRC,
          PATHS.MODULES
        ]
      },
      externals: {
        jquery: 'jQuery'
      }
    }
  ];
};

// Define Module Exports:

module.exports = (env = {development: true}) => {
  process.env.NODE_ENV = (env.production ? 'production' : 'development');
  console.log(`Running in ${process.env.NODE_ENV} mode...`);
  return config(process.env.NODE_ENV);
};
