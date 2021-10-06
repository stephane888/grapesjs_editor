const path = require("path");
const libraryDirectory = "libraries/js";
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const devMode = process.env.NODE_ENV !== "production";

const librairies = [
  "drupal-asset",
  "drupal-storage",
  "drupal-basic-blocks",
  "drupal-blocks",
  "drupal-fields",
];
const plugins = [];

plugins.push(
  new MiniCssExtractPlugin({
    filename: "../css/[name].css",
    chunkFilename: "[id].css",
  })
);

module.exports = {
  plugins,
  context: path.resolve(__dirname, libraryDirectory),
  entry: librairies.reduce(
    function (accumulator, plugin) {
      accumulator[plugin] = {
        import: `./plugins/${plugin}/${plugin}.js`,
        filename: `plugins/${plugin}/[name].min.js`,
      };
      return accumulator;
    },
    {
      "drupal-grapesjs-editor": "./drupal-grapesjs-editor.js",
    }
  ),
  output: {
    path: path.resolve(__dirname, libraryDirectory),
    filename: "[name].min.js",
    library: "[name]",
    libraryTarget: "umd",
  },
  devtool: devMode ? "inline-source-map" : false,
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: ["babel-loader"],
      },
      //règles de compilations pour les fichiers .css
      {
        test: /\.(sa|sc|c)ss$/,
        use: [
          {
            loader: MiniCssExtractPlugin.loader,
            options: {
              //publicPath: "../../",
            },
          },
          {
            loader: "css-loader",
            options: {
              importLoaders: 1,
            },
          },
          {
            loader: "resolve-url-loader", // améliore la résolution des chemins relatifs
            // (utile par exemple quand une librairie tierce fait référence à des images ou des fonts situés dans son propre dossier)
            options: {
              publicPath: "../images",
            },
          },
          {
            loader: "sass-loader",
            options: {
              sourceMap: true, // il est indispensable d'activer les sourcemaps pour que postcss fonctionne correctement
              implementation: require("sass"),
            },
          },
        ],
      },
      // chargement des fichiers htmls;
      {
        test: /\.html$/i,
        loader: "html-loader",
      },
    ],
  },
  externals: { jquery: "jQuery" },
  watch: true,
  watchOptions: {
    ignored: /node_modules/,
  },
};
