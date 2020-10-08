const path = require('path');
const libraryDirectory = 'libraries/js';
const plugins = [
  'drupal-asset',
  'drupal-storage',
  'drupal-basic-blocks',
  'drupal-blocks',
  'drupal-fields',
];

module.exports = {
  context: path.resolve(__dirname, libraryDirectory),
  entry: plugins.reduce(function (accumulator, plugin) {
    accumulator[plugin] = {
      import: `./plugins/${plugin}/${plugin}.js`,
      filename: `plugins/${plugin}/[name].min.js`
    };
    return accumulator;
  }, {
    'drupal-grapesjs-editor': './drupal-grapesjs-editor.js',
  }),
  output: {
    path: path.resolve(__dirname, libraryDirectory),
    filename: '[name].min.js',
    library: '[name]',
    libraryTarget: 'umd',
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: ['babel-loader']
      }
    ]
  },
  externals: {jquery: 'jQuery'},
  watch: true,
  watchOptions: {
    ignored: /node_modules/
  }
};
