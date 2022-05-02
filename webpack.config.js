"use strict";
let path = require('path'); // для работы с path
let HtmlWebpackPlugin = require('html-webpack-plugin');
let { CleanWebpackPlugin } = require('clean-webpack-plugin');
const  TerserPlugin = require('terser-webpack-plugin');
const VueLoaderPlugin = require('vue-loader/lib/plugin');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

require("@babel/core");
require("@babel/polyfill");

module.exports = {
    entry: {
        admin: ["@babel/polyfill", './resources/js/admin/admin-main.js'],
        public: ["@babel/polyfill", './resources/js/public/app.js'],
        publicstyle: './resources/scss/public/style.scss',
        //mobile:["@babel/polyfill", './resources/js/public-mobile/app.js']
    },
    mode: 'development',
    output: {
        publicPath: '/build/dist/',
        path: path.resolve(__dirname, 'public/build/dist'),
        filename: 'app.[name].[contenthash].js',
    },
    optimization: {
        splitChunks: {
            chunks: 'all',
        },
        minimize: true,
        minimizer: [new TerserPlugin()],
    },
    resolve: {
        extensions: ['.js', '.vue', '.json'],
        alias: {
            'vue$': 'vue/dist/vue.esm.js',
        }
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /(node_modules|bower_components)/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['@babel/preset-env']
                    }
                }
            },
            {
                test: /\.vue$/,
                loader: 'vue-loader',
            },
            {
                test:  /\.s[ac]ss$/i,
                use: [
                    MiniCssExtractPlugin.loader,
                    // Creates `style` nodes from JS strings
                    //"style-loader",
                    // Translates CSS into CommonJS
                    "css-loader",
                    // Compiles Sass to CSS
                    "sass-loader",
                ],
            },
            {
                test: /\.css$/,
                use: [
                    'vue-style-loader',
                    'css-loader',
                ],
            },
            {
                test: /\.svg$/,
                use: ['vue-svg-loader'],
                //use: ['svg-inline-loader', 'vue-svg-loader'],
            },
        ]
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: "[name].[contenthash].css",
            chunkFilename: "[id].css"
        }),
        new VueLoaderPlugin(),
        new CleanWebpackPlugin(),
        new HtmlWebpackPlugin({
            chunks:['admin'],
            // Load a custom template (lodash by default)
            template: './resources/views/admin/layouts/webpack.html',
            filename: './../../../resources/views/admin/layouts/webpack.twig'
        }),
        // new HtmlWebpackPlugin({
        //     chunks:['mobile'],
        //     // Load a custom template (lodash by default)
        //     template: './resources/views/public-mobile/layouts/webpack.html',
        //     filename: './../../../resources/views/public-mobile/layouts/webpack.twig'
        // }),
        new HtmlWebpackPlugin({
            chunks:['public'],
            // Load a custom template (lodash by default)
            template: './resources/views/public/layouts/webpack.html',
            filename: './../../../resources/views/public/layouts/webpack.twig'
        }),
        new HtmlWebpackPlugin({
            chunks: ['publicstyle'],
            // Load a custom template (lodash by default)
            template: './resources/views/public/layouts/webpack.html',
            filename: './../../../resources/views/public/layouts/webpackStyle.twig'
        }),
    ],
    watch: true,
    watchOptions: {
        aggregateTimeout: 1000,
        poll: 1000,
        ignored: /node_modules/
    },

}