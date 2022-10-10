module.exports = {
    outputDir: '../static',
    devServer: {
        proxy: 'https://edzima.vestra.edzima.net'
    },
    filenameHashing: false,
    chainWebpack: config => {
        if (process.env.NODE_ENV === 'development') {
            config
            .plugin('html')
            .tap(args => {
                //  args[0] contains the plugin's options object
                args[0].template = 'public/index-development.html'
                return args
            });

        }

        if (process.env.NODE_ENV === 'production') {
            config.plugin("copy").tap(opts => {
                opts[0][0].ignore.push({glob: "index-development.html"});
                return opts;
            });
        }

    }
}
