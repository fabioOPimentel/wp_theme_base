const webpack = require("webpack");
const TerserPlugin = require('terser-webpack-plugin');
const CompressionPlugin = require('compression-webpack-plugin');
const path = require('path');

module.exports = {
    mode: 'production',
    entry: path.resolve(__dirname, 'assets/js/template.js'),
    output: {
      path: path.resolve(__dirname, 'dist'),
      filename: '[name].js',
    },
    module: {
        rules: [
            {
              test: /\.m?js$/,
              exclude: /(node_modules|bower_components)/,
              use: {
                loader: 'babel-loader',
                options: {
                  presets: ['@babel/preset-env'],
                  plugins: ['@babel/plugin-proposal-object-rest-spread']
                }
              }
            }
        ]
    },
    resolve: {
        alias: {
            'vue$': 'vue/dist/vue.esm.js'
        }
    },
    optimization: {
      minimize: true,
      minimizer: [new TerserPlugin({
        sourceMap: true,
        parallel: true,
        cache: './.build_cache/terser',
        exclude: /transpiledLibs/,
        terserOptions: {
          warnings: false,
          ie8: false
        }
      })],
      splitChunks: {
        chunks: 'all',
      }
    },
    plugins: [
      new webpack.DefinePlugin({
        'process.env.NODE_ENV': JSON.stringify('production')
      }),
      new CompressionPlugin({
        filename: "[path].gz[query]",
        algorithm: "gzip",
        test: /\.(js|css)$/,
      })
  ]
}