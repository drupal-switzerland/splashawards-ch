const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');

const rootDir = path.resolve(__dirname);  // Path to your project root
const distDir = path.resolve(__dirname, 'dist');  // Output directory for assets

module.exports = {
  mode: 'production',
  stats: {
    errorDetails: true,
  },
  entry: path.join(rootDir, 'js/scripts.js'),
  output: {
    path: distDir,
    filename: 'bundle.js',
  },
  module: {
    rules: [
      {
        test: /\.css$/,
        exclude: /node_modules/,
        use: [
          MiniCssExtractPlugin.loader,  // Extract CSS into a separate file
          {
            loader: 'css-loader',
            options: {
              sourceMap: true,  // Enable source maps for CSS
              url: true,  // Resolve URLs in CSS
            },
          },
          {
            loader: 'postcss-loader',
            options: {
              sourceMap: true,  // Enable source maps for PostCSS
            },
          },
        ],
      },
    ],
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: 'styles.css',  // The output CSS file
    }),
  ],
  optimization: {
    minimize: true,  // Enable minimization for all assets
    minimizer: [
      new CssMinimizerPlugin(),  // Minify CSS files
    ],
  },
};
