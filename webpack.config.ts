import * as path from 'path';
import webpack from 'webpack';

//import * as MiniCssExtractPlugin from 'mini-css-extract-plugin';
//import * as FixStyleOnlyEntriesPlugin from 'webpack-fix-style-only-entries';
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const FixStyleOnlyEntriesPlugin = require('webpack-fix-style-only-entries');
const TerserPlugin = require("terser-webpack-plugin");
const LiveReloadPlugin = require("webpack-livereload-plugin");

const inputDirectory = path.resolve(__dirname, 'alt');
const outputDirectory = path.resolve(__dirname, 'public_html');

const webpackConfig = (env: string, args: any): webpack.Configuration => {
	/** 本番用か */
	const isProduction = args.mode === 'production';

	const conf: webpack.Configuration = {
		mode: args.mode,

		// 名前に親ディレクトリを含めること(JS/CSSごちゃまぜ回避方法不明)
		entry: {
			// 共通スクリプト
			"scripts/script": path.join(inputDirectory, './scripts/entry/script.ts'),
			"scripts/plugin_edit": path.join(inputDirectory, './scripts/entry/plugin_edit.ts'),
			"scripts/user_edit": path.join(inputDirectory, './scripts/entry/user_edit.ts'),
			// 管理側
			"scripts/plugin_category": path.join(inputDirectory, './scripts/entry/plugin_category.ts'),
			"scripts/log_list": path.join(inputDirectory, './scripts/entry/log_list.ts'),
			"scripts/database_maintenance": path.join(inputDirectory, './scripts/entry/database_maintenance.ts'),
			"scripts/feedback": path.join(inputDirectory, './scripts/entry/feedback.ts'),
			// 共通スタイル
			"styles/style": path.join(inputDirectory, './styles/style.scss'),
		},

		devtool: isProduction ? false : 'inline-source-map',

		output: {
			filename: isProduction
				? '[name].min.js'
				: '[name].js',
			path: outputDirectory
		},

		module: {
			rules: [
				// スクリプト
				{
					test: /\.ts$/,
					use: 'ts-loader',
					exclude: /node_modules/,
				},
				// スタイルシート
				{
					test: /(\.(s[ac])ss)|(\.css)$/,
					use: [
						MiniCssExtractPlugin.loader,
						{
							loader: "css-loader",
							options: {
								sourceMap: !isProduction,
							}
						},
						{
							loader: "postcss-loader",
							options: {
								postcssOptions: {
									plugins: [
										[
											"autoprefixer",
											{ grid: true }
											// 対応ブラウザは package.json: browserslist 参照
										]
									]
								}
							}
						},
						{
							loader: "sass-loader",
							options: {
								sourceMap: !isProduction,
							}
						}
					],
					exclude: /node_modules/,
				}
			],
		},
		resolve: {
			extensions: [
				'.ts', '.js',
			],
		},
		plugins: [
			new FixStyleOnlyEntriesPlugin(),
			new MiniCssExtractPlugin({
				filename: isProduction
					? '[name].min.css'
					: '[name].css'
				,
			}),
			new LiveReloadPlugin(),
		],
	};

	if (isProduction) {
		conf.optimization = {
			minimize: true,
			minimizer: [
				new TerserPlugin({
					terserOptions: {
						compress: {
							pure_funcs: [
								'console.assert',
								'console.trace',
								'console.debug',
								'console.table',
							]
						}
					}
				})
			],
		}
	}

	return conf;
}

export default webpackConfig;

