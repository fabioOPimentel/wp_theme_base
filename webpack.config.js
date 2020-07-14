const webpack = require("webpack");
const TerserPlugin = require('terser-webpack-plugin');
const CompressionPlugin = require('compression-webpack-plugin');
const VueLoaderPlugin = require('vue-loader/lib/plugin')
const path = require('path');

module.exports = {
  mode: 'production',
  entry: path.resolve(__dirname, 'src/js/app.js'),
  output: {
    path: path.resolve(__dirname, 'dist/js'),
    filename: '[name].[hash].js',
  },
  module: {
    rules: [{
        test: /\.vue$/,
        loader: 'vue-loader'
      },
      {
        test: /\.css$/i,
        use: ['style-loader', 'css-loader'],
      },
      {
        test: /\.m?js$/,
        exclude: /node_modules\/(?!(BootstrapVue|IconsPlugin|vue-youtube))/,
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
      automaticNameDelimiter: '.',
      automaticNameMaxLength: 20,
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
    }),
    new VueLoaderPlugin()
  ]
}