import path from "node:path";
import { defineConfig } from '@rspack/cli';
import { rspack } from '@rspack/core';

const isProduction = process.env.NODE_ENV === 'production';

const inputDirectory = path.resolve(__dirname, 'alt');
const outputDirectory = path.resolve(__dirname, 'public_html');

export default defineConfig({
	// 名前に親ディレクトリを含めること(JS/CSSごちゃまぜ回避方法不明)
	entry: {
		// 共通スクリプト
		"scripts/script": path.join(inputDirectory, './scripts/entry/script.ts'),
		"scripts/plugin_edit": path.join(inputDirectory, './scripts/entry/plugin_edit.ts'),
		"scripts/user_edit": path.join(inputDirectory, './scripts/entry/user_edit.ts'),
		// 管理側
		"scripts/management_plugin_category": path.join(inputDirectory, './scripts/entry/management_plugin_category.ts'),
		"scripts/management_log_list": path.join(inputDirectory, './scripts/entry/management_log_list.ts'),
		"scripts/management_database_maintenance": path.join(inputDirectory, './scripts/entry/management_database_maintenance.ts'),
		"scripts/management_feedback_list": path.join(inputDirectory, './scripts/entry/management_feedback_list.ts'),
		"scripts/management_crash_report_list": path.join(inputDirectory, './scripts/entry/management_crash_report_list.ts'),
		"scripts/management_version": path.join(inputDirectory, './scripts/entry/management_version.ts'),
		// 共通スタイル
		"styles/style": path.join(inputDirectory, './styles/style.scss'),
	},

	output: {
		filename: isProduction
			? '[name].min.js'
			: '[name].js',
		path: outputDirectory
	},

	// devServer: {
	// 	// the configuration of the development server
	// 	port: 8080
	// },

	// module: {
	// 	rules: [
	// 	  {
	// 		test: /\.ts$/,
	// 		exclude: [/node_modules/],
	// 		loader: 'builtin:swc-loader',
	// 		options: {
	// 		  jsc: {
	// 			parser: {
	// 			  syntax: 'typescript',
	// 			},
	// 		  },
	// 		},
	// 		type: 'javascript/auto',
	// 	  },
	// 	],
	//   },

	resolve: {
		extensions: [
			'...', '.ts',
		]
	},

	module: {
		rules: [
			{
				test: /\.ts$/,
				exclude: [/node_modules/],
				loader: 'builtin:swc-loader',
				options: {
					jsc: {
						parser: {
							syntax: 'typescript',
						},
					},
				},
			},
			{
				test: /\.(sass|scss)$/,
				use: [
					rspack.CssExtractRspackPlugin.loader,
					{ loader: 'css-loader' },
					{ loader: 'postcss-loader' },
					{ loader: 'sass-loader' },
				],
			},
		],
	},


	plugins: [
		new rspack.DefinePlugin({
		}),
		new rspack.CssExtractRspackPlugin({
			filename: isProduction
				? '[name].min.css'
				: '[name].css'
			,
		}),
	],
})
