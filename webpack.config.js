const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');

module.exports = (env, argv) => {
    const isProduction = argv.mode === 'production';

    return {
        entry: {
            theme: path.resolve(__dirname, 'assets/scss/style.scss'),
            // elementor: path.resolve(__dirname, 'elementor/assets/style.scss'),
            // woocommerce: path.resolve(__dirname, 'woocommerce/assets/scss/style.scss'),
        },

        output: {
            path: path.resolve(__dirname),
            filename: 'assets/temp/[name].js',
            clean: false,
        },

        module: {
            rules: [
                {
                    test: /\.scss$/,
                    use: [
                        MiniCssExtractPlugin.loader,
                        'css-loader',
                        'postcss-loader',
                        {
                            loader: 'sass-loader',
                            options: {
                                sassOptions: {
                                    quietDeps: true,
                                },
                            },
                        },
                    ],
                },
            ],
        },

        plugins: [
            new MiniCssExtractPlugin({
                filename: ({ chunk }) => {
                    switch (chunk.name) {
                        case 'theme':
                            return 'assets/css/style.min.css';

                        // case 'elementor':
                        //     return isProduction
                        //         ? 'elementor/assets/style.min.css'
                        //         : 'elementor/assets/style.css';

                        // case 'woocommerce':
                        //     return isProduction
                        //         ? 'woocommerce/assets/css/style.min.css'
                        //         : 'woocommerce/assets/css/style.css';

                        default:
                            return 'assets/css/[name].min.css';
                    }
                },
            }),
        ],

        optimization: {
            minimize: isProduction,
            minimizer: [new CssMinimizerPlugin()],
        },

        mode: isProduction ? 'production' : 'development',
        devtool: isProduction ? false : 'source-map',
        watch: !isProduction,
        stats: 'errors-warnings',
    };
};