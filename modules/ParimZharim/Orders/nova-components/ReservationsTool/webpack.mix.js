let mix = require('laravel-mix');
const webpack = require('webpack');

require('./nova.mix');

mix
  .setPublicPath('dist')
  .js('resources/js/tool.js', 'js')
  .vue({ version: 3 })
  .css('resources/css/tool.css', 'css')
  .nova('meteoro/reservations-tool')
  .webpackConfig({
      resolve: {
          fallback: {
              buffer: require.resolve('buffer/')
          }
      },
      plugins: [
          new webpack.ProvidePlugin({
              Buffer: ['buffer', 'Buffer'],
          }),
      ],
  });
