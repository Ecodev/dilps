// Karma configuration file, see link for more information
// https://karma-runner.github.io/1.0/config/configuration-file.html

// Use puppeteer to control a headless Chrome
process.env.CHROME_BIN = require('puppeteer').executablePath()

module.exports = function(config) {
    config.set({
        basePath: '',
        frameworks: ['jasmine', '@angular/cli'],
        plugins: [
            require('karma-jasmine'),
            require('karma-chrome-launcher'),
            require('karma-jasmine-html-reporter'),
            require('karma-coverage-istanbul-reporter'),
            require('karma-scss-preprocessor'),
            require('@angular/cli/plugins/karma')
        ],
        files: [
            { pattern: './src/styles.scss', included: true, watched: true }
        ],
        preprocessors: {
            './src/styles.scss': ['scss']
        },
        client: {
            clearContext: false, // leave Jasmine Spec Runner output visible in browser
            captureConsole: true
        },
        coverageIstanbulReporter: {
            reports: ['html', 'lcovonly'],
            fixWebpackSourcePaths: true
        },
        angularCli: {
            environment: 'dev'
        },
        reporters: ['progress', 'kjhtml'],
        port: 9876,
        colors: true,
        logLevel: config.LOG_INFO,
        autoWatch: true,
        browsers: ['Chrome'],
        singleRun: false,
        browserNoActivityTimeout: 600000, // Wait 10 minutes before assuming browser crashed
        browserDisconnectTolerance: 10,
        browserDisconnectTimeout : 500000
    });
};
