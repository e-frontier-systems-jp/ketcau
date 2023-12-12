const path = require('path');
const webpack = require('webpack');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

module.exports = {
    mode: 'production',
    entry: {
        front: './html/template/default/assets/js/bundle.js',
        admin: './html/template/admin/assets/js/bundle.js'
    },
    output: {
        path: path.resolve(__dirname, 'html/bundle'),
        filename: '[name].bundle.js'
    },
    resolve: {
        alias: {
            jquery: path.join(__dirname, 'node_modules', 'jquery')
        }
    },
    module: {
        rules: [
            {
                test: /\.(scss|sass|css)/i,
                use: [
                    MiniCssExtractPlugin.loader,
                    {
                        loader: 'css-loader',
                        options: { url: false }
                    },
                    'sass-loader'
                ]
            },
            // {
            //     test: /\.png|jpg|svg|gif|eot|wof|woff|ttf$/,
            //     use: [ 'url-loader' ]
            // },
            // {
            //     test: /\.js$/,
            //     use: [
            //         {
            //             loader: 'babel-loader',
            //             options: {
            //                 presets: ['@babel/preset-env']
            //             }
            //         }
            //     ],
            //     exclude: /node_modules/
            // }
        ]
    },
    plugins: [
        new webpack.ProvidePlugin({
            $: "jquery",
            jQuery: "jquery",
            "window.jQuery": "jquery"
        }),
        new MiniCssExtractPlugin({
            filename: "[name].bundle.css"
        })
    ],
    devtool: 'source-map',
    watchOptions: {
        ignored: /node_modules/
    }
};
