import path, { dirname } from 'node:path';
import { Glob } from 'glob';
import ForkTsCheckerWebpackPlugin from 'fork-ts-checker-webpack-plugin';
import MiniCssExtractPlugin from 'mini-css-extract-plugin';
import RemovePlugin from 'remove-files-webpack-plugin';
import StylelintPlugin from 'stylelint-webpack-plugin';
import SpriteLoaderPlugin from 'svg-sprite-loader/plugin.js';
import * as embeddedSass from 'sass-embedded';
import { fileURLToPath } from 'node:url';

const __dirname =
  import.meta.dirname ?? dirname(fileURLToPath(import.meta.url));

async function gatherProjectFiles() {
  const jsFiles = {};
  const scssFiles = {};
  const jsGlob = new Glob('source/**/!(*.stories).{cjs,js,ts}', {
    ignore: ['**/_*', 'source/@types/**', 'source/08-react/**'],
  });
  const scssGlob = new Glob('source/**/*.scss', jsGlob);
  for await (const currentFile of jsGlob.iterate()) {
    const filePaths = currentFile.split(path.sep);
    const sourceDirIndex = filePaths.indexOf('source');
    if (sourceDirIndex >= 0) {
      const fileName = path.basename(currentFile).replace(/\.c?[jt]s$/, '');
      const newFilePath = `js/${fileName}`;
      // Throw an error if duplicate files detected.
      if (jsFiles[newFilePath]) {
        throw new Error(`More than one file named ${fileName}.[jt]s found.`);
      }
      jsFiles[newFilePath] = {
        import: path.resolve(__dirname, currentFile),
      };
    }
  }

  for await (const currentFile of scssGlob.iterate()) {
    const filePaths = currentFile.split(path.sep);
    const sourceDirIndex = filePaths.indexOf('source');
    if (sourceDirIndex >= 0) {
      const fileName = path.basename(currentFile, '.scss');
      const newFilePath = `css/${fileName}`;
      // Throw an error if duplicate files detected.
      if (scssFiles[newFilePath]) {
        throw new Error(`More that one file named ${fileName}.scss found.`);
      }
      scssFiles[newFilePath] = {
        import: `./${currentFile}`,
      };
    }
  }
  return {
    ...jsFiles,
    ...scssFiles,
  };
}

const commonConfig = {
  entry: () => gatherProjectFiles(),
  plugins: [
    new MiniCssExtractPlugin(),
    new RemovePlugin({
      after: {
        test: [
          {
            folder: './dist/css',
            method: absolutePath => /\.js(\.map)?$/m.test(absolutePath),
            recursive: true,
          },
        ],
        log: false,
        logError: true,
        logWarning: false,
      },
    }),
    new StylelintPlugin({
      exclude: ['node_modules', 'dist', 'storybook'],
    }),
    new SpriteLoaderPlugin(),
    new ForkTsCheckerWebpackPlugin(),
  ],
  context: __dirname,
  module: {
    rules: [
      {
        test: /\.(ts|tsx)$/,
        loader: 'ts-loader',
        exclude: /node_modules/,
        options: {
          // We will check types in fork plugin
          transpileOnly: true,
        },
        resolve: {
          fullySpecified: false,
        },
      },
      {
        test: /\.(js|jsx)$/,
        exclude: /node_modules/,
        use: ['swc-loader'],
        resolve: {
          fullySpecified: false,
        },
      },
      {
        test: /\.scss$/i,
        exclude: /node_modules/,
        use: [
          {
            loader: MiniCssExtractPlugin.loader,
            options: {
              publicPath: '../',
            },
          },
          {
            loader: 'css-loader',
            options: {
              esModule: false,
              // Ignore /core/ URLs
              url: {
                filter: url => !url.includes('/core/'),
              },
            },
          },
          'postcss-loader',
          {
            loader: 'sass-loader',
            options: {
              implementation: embeddedSass,
              webpackImporter: false,
              sassOptions: {
                loadPaths: [
                  path.resolve(__dirname, 'source'),
                  './node_modules/@uswds/uswds/packages',
                ],
                // Hiding mixed declaration warnings for now.
                // https://sass-lang.com/documentation/breaking-changes/mixed-decls/
                silenceDeprecations: ['mixed-decls'],
                // Hiding dependency warnings due to deprecation warnings from USWDS.
                quietDeps: true,
              },
            },
          },
        ],
      },
      {
        test: /images\/_sprite-source-files\/.*\.svg$/,
        exclude: /node_modules/,
        use: [
          {
            loader: 'svg-sprite-loader',
            options: {
              extract: true,
              spriteFilename: 'sprite.artifact.svg',
              outputPath: 'images/',
            },
          },
          'svg-transform-loader',
          'svgo-loader',
        ],
      },
      {
        test: /fonts\/.*\.(woff2?|ttf|otf|eot|svg)(\?v=\d+\.\d+\.\d+)?$/i,
        exclude: ['/node_modules/'],
        type: 'asset/resource',
        generator: {
          filename: 'fonts/[name][ext][query]',
        },
      },
      {
        test: /\.(png|svg|jpg|gif|webp)$/i,
        exclude: [/images\/_sprite-source-files\/.*\.svg$/, '/node_modules/'],
        type: 'asset',
        generator: {
          filename: 'images/backgrounds/[hash][ext][query]',
        },
      },
    ],
  },
  externals: {
    drupal: 'Drupal',
    drupalSettings: 'drupalSettings',
    once: 'once',
  },
  resolve: {
    extensions: ['.js', '.jsx', '.ts', '.tsx'],
    extensionAlias: {
      '.es6': ['.es6.ts', '.es6.js'],
    },
    modules: [path.resolve(__dirname, 'source'), 'node_modules'],
    enforceExtension: false,
  },
  output: {
    path: path.resolve(__dirname, 'dist'),
    clean: false,
  },
  stats: 'minimal',
};

export default commonConfig;
