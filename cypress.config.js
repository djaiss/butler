const { defineConfig } = require('cypress');

module.exports = defineConfig({
  chromeWebSecurity: false,
  retries: 2,
  defaultCommandTimeout: 5000,
  watchForFileChanges: false,
  videosFolder: 'tests/cypress/videos',
  screenshotsFolder: 'tests/cypress/screenshots',
  fixturesFolder: 'tests/cypress/fixture',
  e2e: {
    setupNodeEvents(on, config) {
      return require('./tests/cypress/plugins/index.js')(on, config);
    },
    baseUrl: 'https://ross.test',
    specPattern: 'tests/cypress/integration/**/*.cy.{js,jsx,ts,tsx}',
    supportFile: 'tests/cypress/support/index.js',
  },
});
