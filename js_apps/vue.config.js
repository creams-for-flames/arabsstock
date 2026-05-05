module.exports = {
  publicPath: process.env.NODE_ENV === 'production'
    ? '/js_apps_assets/'
    : '/',
  indexPath: 'index.html',
  outputDir: '../public/js_apps_assets',
  productionSourceMap: false,
  configureWebpack: {
    optimization: {
      splitChunks: false
    }
  },
  filenameHashing: false,
  pages: {
    home: 'src/home/main.js',
    image_store: 'src/image_store/main.js',
    image_store_remove_bg: 'src/image_store_remove_bg/main.js',
    image_store_contributor: 'src/image_store_contributor/main.js',
    image_store_review: 'src/image_store_review/main.js',
  },
}
